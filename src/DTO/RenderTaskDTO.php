<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения задачи в списке
 */
readonly final class RenderTaskDTO
{
    /**
     * @param int $id ID задания
     * @param string $name Название задания
     * @param string $description Описание
     * @param string $category Название категории
     * @param string $city Название города
     * @param int $budget Бюджет
     * @param string $createdAt Дата создания
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $category,
        public string $city,
        public int $budget,
        public string $createdAt,
    ) {
    }
}