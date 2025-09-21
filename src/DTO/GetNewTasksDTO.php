<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для фильтрации и получения списка новых заданий
 */
readonly final class GetNewTasksDTO
{
    /**
     * @param string|array $categories ID категорий для фильтрации
     * @param bool $checkWorker Флаг для фильтрации задач без исполнителя
     * @param string $createdAt Период создания для фильтрации
     * @param int $offset Смещение для пагинации
     * @param int $limit Лимит записей для пагинации
     */
    public function __construct(
        public string|array $categories,
        public bool $checkWorker,
        public string $createdAt,
        public int $offset = 0,
        public int $limit = 0,
    ) {
    }
}