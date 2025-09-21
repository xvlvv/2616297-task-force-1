<?php

declare(strict_types = 1);

namespace Xvlvv\Repository;

use app\models\Review;
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
        // TODO: Implement save() method.
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