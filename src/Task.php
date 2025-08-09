<?php

namespace Xvlvv;

use Xvlvv\Enums\Action;
use Xvlvv\Enums\Status;

/**
 * Представляет сущность «Задание». Определяет списки действий и статусов, а также выполняет базовую работу с ними
 */
final class Task
{
    /**
     * Текущий статус задания
     *
     * @var Status
     */
    private Status $currentStatus = Status::NEW;

    /**
     * Инициализирует ID заказчика и исполнителя
     *
     * @param int $customerId
     * @param int $workerId
     * @param TaskStateManager $taskStateManager
     */
    public function __construct(
        private int $customerId,
        private int $workerId,
        private readonly TaskStateManager $taskStateManager,
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