<?php

declare(strict_types = 1);

namespace Xvlvv\DTO;

/**
 * DTO для отображения публичного профиля пользователя
 */
readonly final class ViewUserDTO
{
    /**
     * @param string $name Имя
     * @param string|null $avatarPath Путь к аватару
     * @param string $description Описание пользователя
     * @param string $bio Биография
     * @param array $specializations Массив специализаций
     * @param int $completedTasks Количество выполненных заданий
     * @param int $failedTasks Количество проваленных заданий
     * @param float $rating Рейтинг
     * @param int $ratingPlacement Место в рейтинге
     * @param string $createdAt Дата регистрации
     * @param string $status Статус
     * @param string $phoneNumber Номер телефона
     * @param string $email Электронная почта
     * @param string $telegramUsername Имя пользователя в Telegram
     * @param bool $showContacts Флаг, показывать ли контакты
     * @param array $reviews Массив отзывов
     */
    public function __construct(
        public string $name,
        public ?string $avatarPath,
        public string $description,
        public string $bio,
        public array $specializations,
        public int $completedTasks,
        public int $failedTasks,
        public float $rating,
        public int $ratingPlacement,
        public string $createdAt,
        public string $status,
        public string $phoneNumber,
        public string $email,
        public string $telegramUsername,
        public bool $showContacts,
        public array $reviews,
    ) {
    }
}