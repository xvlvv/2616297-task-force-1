<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для старта работы над заданием
 */
readonly final class StartTaskDTO
{
    /**
     * @param int $taskId ID задания
     * @param int $customerId ID заказчика
     * @param int $workerId ID назначенного исполнителя
     */
    public function __construct(
        public int $taskId,
        public int $customerId,
        public int $workerId,
    ) {
    }
}