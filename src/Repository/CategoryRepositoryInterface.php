<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\Category;

interface CategoryRepositoryInterface
{
    public function getById(int $id): ?Category;
    public function getByIdOrFail(int $id): Category;
}