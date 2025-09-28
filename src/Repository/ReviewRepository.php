<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\Review;
use RuntimeException;
use Xvlvv\DTO\CreateReviewDTO;

/**
 * Репозиторий для работы с отзывами
 */
class ReviewRepository implements ReviewRepositoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function save(CreateReviewDTO $dto): bool
    {
        $review = new Review();
        $review->task_id = $dto->taskId;
        $review->worker_id = $dto->workerId;
        $review->customer_id = $dto->authorId;
        $review->comment = $dto->comment;
        $review->rating = $dto->rating;

        if (!$review->save()) {
            throw new RuntimeException('Failed to save review');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewsCountByUser(int $id): int
    {
        return Review::find()->where(['worker_id' => $id])->count();
    }


    /**
     * {@inheritdoc}
     */
    public function getReviewsByUser(int $id): array
    {
        return Review::find()
            ->with('customer', 'task')
            ->where(['worker_id' => $id])->all();
    }
}