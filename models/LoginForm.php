<?php

namespace app\models;

use Xvlvv\Entity\User;
use Xvlvv\Services\Application\AuthService;
use yii\base\Model;

/**
 * Модель формы для входа пользователя на сайт.
 */
class LoginForm extends Model
{
    /** @var string Email пользователя */
    public string $email = '';
    /** @var string Пароль пользователя */
    public string $password = '';
    /** @var User|null Доменная сущность пользователя, найденная после успешной валидации */
    private ?User $_user = null;
    /** @var AuthService Сервис для аутентификации */
    private AuthService $authService;

    /**
     * @param AuthService $authService Сервис для аутентификации.
     * @param array $config
     */
    public function __construct(AuthService $authService, array $config = [])
    {
        $this->authService = $authService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required', 'message' => 'Обязательное поле'],
            [['email', 'password'], 'safe'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Кастомный валидатор для проверки пароля.
     * Использует `AuthService` для проверки учетных данных.
     * В случае успеха, сохраняет найденную сущность пользователя.
     *
     * @param string $attribute Атрибут для валидации (в данном случае 'password').
     * @param array|null $params Дополнительные параметры.
     */
    public function validatePassword(string $attribute, $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->authService->authenticate($this->email, $this->password);

        if ($user === null) {
            $this->addError($attribute, 'Неправильный email или пароль');
            return;
        }

        $this->_user = $user;
    }

    /**
     * Возвращает доменную сущность пользователя после успешной аутентификации.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->_user;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }
}