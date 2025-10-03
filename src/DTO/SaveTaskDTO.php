<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\Entity\Category;
use Xvlvv\Entity\City;
use Xvlvv\Entity\User;

/**
 * DTO для сохранения задачи в хранилище
 */
readonly final class SaveTaskDTO
{
    /**
     * @param string $name Название задания
     * @param string $description Описание
     * @param Category $category Объект категории
     * @param User $customer Объект пользователя-заказчика
     * @param \DateTimeImmutable|null $endDate Срок выполнения
     * @param Coordinates|null $coordinates Координаты
     * @param int|null $budget Бюджет
     * @param City|null $city Объект города
     * @param array $files Массив ID файлов
     */
    public function __construct(
        public string $name,
        public string $description,
        public Category $category,
        public User $customer,
        public ?\DateTimeImmutable $endDate = null,
        public ?Coordinates $coordinates = null,
        public ?string $locationAdditionalInfo = null,
        public ?int $budget = null,
        public ?City $city = null,
        public array $files = []
    ) {
    }
}