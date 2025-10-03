<?php

declare(strict_types=1);

namespace Xvlvv\DTO;

use yii\web\UploadedFile;

/**
 * DTO для передачи данных при обновлении профиля пользователя.
 * Собирает данные из ProfileEditForm и передает их в ProfileEditService.
 */
final readonly class UpdateProfileDTO
{
    /**
     * @param string $name Новое имя пользователя.
     * @param string $email Новый email пользователя.
     * @param string|null $birthday Новая дата рождения.
     * @param string|null $phone Новый номер телефона.
     * @param string|null $telegram Новый юзернейм в Telegram.
     * @param string|null $bio Новая информация "О себе".
     * @param int[] $specializations Массив ID новых специализаций.
     * @param UploadedFile|null $avatarFile Объект загруженного файла аватара, если он был изменен.
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $birthday,
        public ?string $phone,
        public ?string $telegram,
        public ?string $bio,
        public array $specializations,
        public ?UploadedFile $avatarFile = null,
    ) {
    }
}