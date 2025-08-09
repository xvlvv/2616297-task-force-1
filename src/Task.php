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

    /**
     * Список возможных действий для текущего статуса
     *
     * @return array
     */
    public function getAvailableActions(): array
    {
        return $this->taskStateManager->getAvailableActions($this->getCurrentStatus());
    }
}