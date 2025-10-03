<?php

namespace app\models;

use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use yii\base\Model;

/**
 * Модель формы для смены пароля пользователя.
 */
class SecurityForm extends Model
{
    /** @var string Текущий пароль пользователя */
    public string $currentPassword = '';

    /** @var string Новый пароль */
    public string $newPassword = '';

    /** @var string Повтор нового пароля */
    public string $passwordRepeat = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['currentPassword', 'newPassword', 'passwordRepeat'], 'required'],
            ['newPassword', 'string', 'min' => 3, 'tooShort' => 'Пароль должен содержать не менее 3 символов'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают'],
            ['currentPassword', 'validateCurrentPassword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'currentPassword' => 'Текущий пароль',
            'newPassword' => 'Новый пароль',
            'passwordRepeat' => 'Повтор пароля',
        ];
    }

    /**
     * Кастомный валидатор для проверки текущего пароля пользователя.
     *
     * @param string $attribute Атрибут для валидации
     * @param array|null $params Дополнительные параметры
     */
    public function validateCurrentPassword(string $attribute, ?array $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        /** @var UserRepositoryInterface $userRepo */
        $userRepo = Yii::$container->get(UserRepositoryInterface::class);
        $user = $userRepo->getByIdOrFail(Yii::$app->user->id);

        if (!$user->isValidPassword($this->currentPassword)) {
            $this->addError($attribute, 'Неверный текущий пароль');
        }
    }
}