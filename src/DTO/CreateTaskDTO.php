<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

use DateTimeImmutable;

/**
 * DTO для создания нового задания
 */
readonly final class CreateTaskDTO
{
    /**
     * @param string $name Название задания
     * @param string $description Описание задания
     * @param int $categoryId ID категории
     * @param int $customerId ID заказчика
     * @param DateTimeImmutable|null $endDate Срок выполнения
     * @param string|null $latitude Широта местоположения
     * @param string|null $longitude Долгота местоположения
     * @param int|null $budget Бюджет
     * @param int|null $cityId ID города
     * @param SaveTaskFileDTO[] $files Массив прикрепленных файлов
     */
    public function __construct(
        public string $name,
        public string $description,
        public int $categoryId,
        public int $customerId,
        public ?DateTimeImmutable $endDate = null,
        public ?string $latitude = null,
        public ?string $longitude = null,
        public ?int $budget = null,
        public ?int $cityId = null,
        public array $files = []
    ) {
    }
}