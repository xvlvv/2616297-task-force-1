<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\TaskResponse;

interface TaskResponseRepositoryInterface
{
    public function save(TaskResponse $taskResponse): bool;
}