<?php

namespace Xvlvv\DTO;

readonly final class SaveTaskFileDTO
{
    public function __construct(
        public string $originalName,
        public string $filePath,
    ) {
    }
}