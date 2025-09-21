<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для сохранения отзыва
 */
readonly final class SaveReviewDTO
{
    /**
     * @param int $rating Оценка
     * @param string $comment Комментарий
     * @param int $taskId ID задания
     * @param int $authorId ID автора отзыва
     */
    public function __construct(
        public int $rating,
        public string $comment,
        public int $taskId,
        public int $authorId,
    ) {
    }
}