<?php

namespace Xvlvv\Repository;

use Xvlvv\DTO\CreateReviewDTO;

interface ReviewRepositoryInterface
{
    public function save(CreateReviewDTO $dto): bool;

    public function getReviewsCountByUser(int $id): int;
    public function getReviewsByUser(int $id): array;
}