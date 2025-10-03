<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для создания отзыва
 */
readonly final class CreateReviewDTO
{
    /**
     * @param int $taskId ID задания, к которой относится отзыв
     * @param int $authorId ID автора отзыва
     * @param int $workerId ID исполнителя, на которого оставляют отзыв
     * @param string $comment Текст комментария
     * @param int|null $rating Оценка опционально
     */
    public function __construct(
        public int $taskId,
        public int $authorId,
        public int $workerId,
        public string $comment,
        public ?int $rating = null,
    ) {
    }
}