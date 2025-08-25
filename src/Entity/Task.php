<?php

namespace Xvlvv\Entity;

use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;
use Xvlvv\Exception\InvalidActionForTaskException;
use Xvlvv\Services\TaskStateManager;

/**
 * Представляет сущность «Задание». Определяет списки действий и статусов, а также выполняет базовую работу с ними
 */
final class Task
{
    /**
     * Инициализирует ID заказчика и исполнителя
     *
     * @param TaskStateManager $taskStateManager
     * @param int $customerId
     * @param int|null $workerId
     * @param int|null $id
     * @param Status $currentStatus
     * @param City|null $city
     */
    public function __construct(
        private readonly TaskStateManager $taskStateManager,
        private int $customerId,
        private ?int $workerId = null,
        private ?int $id = null,
        private Status $currentStatus = Status::NEW,
        private ?City $city = null,
    ) {
    }

    /**
     * Возвращает текущий статус задания
     *
     * @return Status
     */
    public function getCurrentStatus(): Status
    {
        return $this->currentStatus;
    }

    /**
     * Возвращает следующий статус задания в зависимости от указанного действия, null если действие не найдено либо
     * для текущего статус данное действие невозможно
     *
     * @param Action $action
     * @return Status|null
     */
    public function getNextStatus(Action $action): ?Status
    {
        return $this->taskStateManager->getNextStatus($this->getCurrentStatus(), $action);
    }

    private function applyAction(Action $action): bool
    {
        $actions = $this->taskStateManager->getAllAvailableActions($this->getCurrentStatus());

        if (!in_array($action, $actions)) {
            throw new InvalidActionForTaskException($action, $this->getCurrentStatus());
        }

        $this->setStatus($this->getNextStatus($action));
        return true;
    }

    public function fail(): bool
    {
        return $this->applyAction(Action::FAIL);
    }

    public function finish(): bool
    {
        return $this->applyAction(Action::COMPLETE);
    }

    public function cancel(): bool
    {
        return $this->applyAction(Action::CANCEL);
    }

    public function start(int $workerId): bool
    {
        if (null !== $this->getWorkerId()) {
            throw new \DomainException('Worker already set for this task');
        }
        $this->setWorkerId($workerId);
        $this->applyAction(Action::START);
        return true;
    }

    private function setStatus(Status $status): void
    {
        $this->currentStatus = $status;
    }

    private function setWorkerId(int $workerId): void
    {
        $this->workerId = $workerId;
    }

    /**
     * Список возможных действий для текущего статуса
     *
     * @param int $userId
     * @return array
     */
    public function getAvailableActions(int $userId): array
    {
        $actions = $this->taskStateManager->getPublicAvailableActions($this->getCurrentStatus());

        $filteredActionsByAccessCheck = array_filter(
            $actions,
            function (Action $actionEnum) use ($userId) {
                $actionObject = $actionEnum->getActionObject();
                return $actionObject->canMakeAction($userId, $this->customerId, $this->workerId);
            }
        );

        $availableActionObjects = array_map(
            function (Action $actionEnum) {
                return $actionEnum->getActionObject();
            },
            $filteredActionsByAccessCheck
        );

        return array_values($availableActionObjects);
    }

    public function getWorkerId(): ?int
    {
        return $this->workerId;
    }
}