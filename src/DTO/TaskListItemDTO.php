<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения одного задания в списке (например, на странице "Мои задания").
 * Содержит только необходимую для карточки задания информацию.
 */
final readonly class TaskListItemDTO
{
    /**
     * @param int $id ID задания.
     * @param string $name Название задания.
     * @param int|null $budget Бюджет задания.
     * @param string $createdAt Время создания задания (в формате для отображения).
     * @param string $description Описание задания.
     * @param string $city
     * @param string $category
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?int $budget,
        public string $createdAt,
        public string $description,
        public string $city,
        public string $category
    ) {
    }
}