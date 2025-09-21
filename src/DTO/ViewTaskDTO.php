<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения детальной информации о задании
 */
readonly final class ViewTaskDTO
{
    /**
     * @param string $name Название
     * @param int $budget Бюджет
     * @param string $description Описание
     * @param string $category Название категории
     * @param string $createdAt Дата создания
     * @param string|null $endDate Срок выполнения
     * @param string $status Статус задания
     * @param array $responses Массив откликов
     */
    public function __construct(
        public string $name,
        public int $budget,
        public string $description,
        public string $category,
        public string $createdAt,
        public ?string $endDate,
        public string $status,
        public array $responses,
    ) {
    }
}