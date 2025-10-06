<?php

declare(strict_types=1);

namespace Xvlvv\Repository;

use Xvlvv\DTO\CreateReviewDTO;

/**
 * Интерфейс репозитория для отзывов
 */
interface ReviewRepositoryInterface
{
    /**
     * Сохраняет новый отзыв
     * @param CreateReviewDTO $dto
     * @return bool
     */
    public function save(CreateReviewDTO $dto): bool;


    /**
     * Получает количество отзывов для пользователя
     * @param int $id ID пользователя
     * @return int
     */
    public function getReviewsCountByUser(int $id): int;

    /**
     * Получает все отзывы для пользователя
     * @param int $id ID пользователя
     * @return array
     */
    public function getReviewsByUser(int $id): array;
}