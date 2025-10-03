<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для сохранения отклика на задачу
 */
readonly final class SaveTaskResponseDTO
{
    /** @var bool Флаг, что отклик отклонен */
    public bool $isRejected;

    /**
     * @param int $taskId ID задания
     * @param int $userId ID пользователя, оставившего отклик
     * @param string|null $comment Комментарий к отклику
     * @param int|null $price Цена исполнителя
     */
    public function __construct(
        public int $taskId,
        public int $userId,
        public ?string $comment = null,
        public ?int $price = null,
    ) {
        $this->isRejected = false;
    }
}