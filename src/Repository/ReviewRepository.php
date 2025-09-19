<?php

namespace Xvlvv\Repository;

use app\models\Review;
use Xvlvv\DTO\CreateReviewDTO;

class ReviewRepository implements ReviewRepositoryInterface
{

    public function save(CreateReviewDTO $dto): bool
    {
        // TODO: Implement save() method.
    }

    public function getReviewsCountByUser(int $id): int
    {
        return Review::find()->where(['worker_id' => $id])->count();
    }

    public function getReviewsByUser(int $id): array
    {
        return Review::find()
            ->with('customer', 'task')
            ->where(['worker_id' => $id])->all();
    }
}