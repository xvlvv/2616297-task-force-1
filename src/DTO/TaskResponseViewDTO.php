<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения отклика на задачу
 */
class TaskResponseViewDTO
{
    /**
     * @param int $id ID отклика
     * @param int $userId ID пользователя, оставившего отклик
     * @param string $workerName Имя исполнителя
     * @param float $rating Рейтинг исполнителя
     * @param int $reviewCount Количество отзывов
     * @param string|null $avatarPath Путь к аватару
     * @param string $createdAt Дата создания отклика
     * @param int $price Цена исполнителя
     * @param bool $isRejected
     * @param string|null $comment
     */
    public function __construct(
        public int $id,
        public int $userId,
        public string $workerName,
        public float $rating,
        public int $reviewCount,
        public ?string $avatarPath,
        public string $createdAt,
        public int $price,
        public bool $isRejected,
        public ?string $comment = null,
    ) {
    }
}