<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

use Couchbase\Coordinate;
use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\Enums\Status;

/**
 * DTO для отображения детальной информации о задании
 */
readonly final class ViewTaskDTO
{
    /**
     * @param int $id Идентификатор
     * @param string $name Название
     * @param int $budget Бюджет
     * @param string $description Описание
     * @param string $category Название категории
     * @param string $createdAt Дата создания
     * @param string|null $endDate Срок выполнения
     * @param Status $status Статус задания
     * @param array $availableActions Массив возможных действий над задачей
     * @param array $responses Массив откликов
     * @param array $files Массив файлов
     */
    public function __construct(
        public int $id,
        public string $name,
        public int $budget,
        public string $description,
        public string $category,
        public string $createdAt,
        public ?string $endDate,
        public Status $status,
        public array $availableActions,
        public array $responses,
        public array $files,
        public ?string $cityName,
        public ?Coordinates $coordinates,
        public ?string $additionalInfo,
    ) {
    }
}