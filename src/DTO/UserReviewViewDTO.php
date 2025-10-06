<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения отзыва в профиле пользователя
 */
readonly final class UserReviewViewDTO
{
    /**
     * @param int $taskId Идентификатор задания отзыва
     * @param string $taskName Название задания
     * @param string $comment Комментарий
     * @param int $rating Оценка
     * @param string $createdAt Дата создания
     * @param string|null $avatarPath Путь к аватару автора отзыва
     */
    public function __construct(
        public int $taskId,
        public string $taskName,
        public string $comment,
        public int $rating,
        public string $createdAt,
        public ?string $avatarPath,
    ) {
    }
}