<?php

namespace Xvlvv\DTO;

use Xvlvv\Entity\Task;
use Xvlvv\Entity\User;

readonly final class SaveTaskResponseDTO
{
    public bool $isRejected;

    public function __construct(
        public int $taskId,
        public int $userId,
        public ?string $comment = null,
        public ?int $price = null,
    ) {
        $this->isRejected = false;
    }
}