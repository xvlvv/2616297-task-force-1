<?php

namespace Xvlvv\Repository;

use Xvlvv\DTO\CreateReviewDTO;

interface ReviewRepositoryInterface
{
    public function save(CreateReviewDTO $dto): bool;
}