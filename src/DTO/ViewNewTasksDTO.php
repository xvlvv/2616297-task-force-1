<?php

namespace Xvlvv\DTO;

readonly final class ViewNewTasksDTO
{
    public function __construct(
        public array $tasks,
        public array $categories,
    )
    {

    }
}