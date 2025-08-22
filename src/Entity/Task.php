<?php

namespace Xvlvv\Entity;

use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\Entity\City;
use Xvlvv\DTO\CreateTaskDTO;
use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;
use Xvlvv\Services\TaskStateManager;

/**
 * Представляет сущность «Задание». Определяет списки действий и статусов, а также выполняет базовую работу с ними
 */
final class Task
{
    private int $id;
    /**
     * Инициализирует ID заказчика и исполнителя
     *
     * @param int $customerId
     * @param TaskStateManager $taskStateManager
     * @param int|null $workerId
     * @param Status $currentStatus
     * @param City|null $city
     */
    public function __construct(
        private int $customerId,
        private readonly TaskStateManager $taskStateManager,
        private ?int $workerId = null,
        private Status $currentStatus = Status::NEW,
        private ?City $city = null,
    ) {
    }

    public static function create(
        string $name,
        string $description,
        int $customerId,
        Category $category,
        \DateTimeImmutable $end_date,
        ?Coordinates $coordinates = null,
       ?int $id = null,
    ): self
    {

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

    public function applyAction(Action $action): bool
    {
        $actions = $this->taskStateManager->getAvailableActions($this->getCurrentStatus());

        if (null === $actions) {
            return false;
        }

        if (!in_array($action, $actions)) {
            return false;
        }

        $this->setStatus($this->getNextStatus($action));
        return true;
    }

    private function setStatus(Status $status): void
    {
        $this->currentStatus = $status;
    }

    /**
     * Список возможных действий для текущего статуса
     *
     * @return ?array
     */
    public function getAvailableActions(): ?array
    {
        $actions = $this->taskStateManager->getAvailableActions($this->getCurrentStatus());

        if (null === $actions) {
            return null;
        }

        return array_map(static function (Action $action) {
            return $action->value;
        }, $actions);
    }
}