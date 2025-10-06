<?php

declare(strict_types=1);

namespace Xvlvv\Services\Application;

use Xvlvv\DTO\UpdateProfileDTO;
use Xvlvv\Repository\CategoryRepositoryInterface;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Сервис для редактирования профиля пользователя
 */
readonly final class ProfileEditService
{
    /**
     * @param UserRepositoryInterface $userRepository Репозиторий для работы с пользователями
     * @param CategoryRepositoryInterface $categoryRepository Репозиторий для работы с категориями
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Обновляет профиль пользователя на основе полученных данных
     *
     * @param int $userId ID пользователя, чей профиль обновляется
     * @param UpdateProfileDTO $dto DTO с новыми данными профиля
     * @return void
     * @throws ServerErrorHttpException в случае ошибки сохранения аватара
     */
    public function update(int $userId, UpdateProfileDTO $dto): void
    {
        $user = $this->userRepository->getByIdOrFail($userId);

        $user->changeName($dto->name);
        $user->changeEmail($dto->email);

        if ($dto->avatarFile) {
            $avatarPath = $this->saveAvatar($dto->avatarFile);
            $user->changeAvatar($avatarPath);
        }

        $categoryEntities = $this->categoryRepository->getByIds($dto->specializations);

        $profile = $user->getProfile();

        $profile->updateDetails(
            $dto->birthday,
            $dto->phone,
            $dto->telegram,
            $dto->bio,
            $categoryEntities
        );

        $this->userRepository->save($user);
    }

    /**
     * Сохраняет загруженный файл аватара в публичную директорию
     *
     * @param UploadedFile $file Объект загруженного файла
     * @return string Путь к сохраненному файлу
     * @throws ServerErrorHttpException в случае ошибки сохранения
     */
    private function saveAvatar(UploadedFile $file): string
    {
        $dir = Yii::getAlias('@webroot/uploads/avatars/');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $fileName = uniqid() . '.' . $file->getExtension();
        $filePath = $dir . $fileName;

        if (!$file->saveAs($filePath)) {
            throw new ServerErrorHttpException('Не удалось сохранить файл аватара.');
        }

        return '/uploads/avatars/' . $fileName;
    }
}