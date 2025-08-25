<?php

namespace Xvlvv\Repository;

use Xvlvv\DTO\SaveTaskResponseDTO;

interface TaskResponseRepositoryInterface
{
    public function save(SaveTaskResponseDTO $dto): bool;
}