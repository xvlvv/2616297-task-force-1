<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения страницы с новыми задачами
 */
readonly final class ViewNewTasksDTO
{
    /**
     * @param array $tasks Массив DTO задач для отображения
     * @param array $categories Массив DTO категорий для фильтра
     */
    public function __construct(
        public array $tasks,
        public array $categories,
    ) {
    }
}