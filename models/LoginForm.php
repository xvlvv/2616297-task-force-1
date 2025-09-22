<?php

namespace app\models;

use Xvlvv\Entity\User;
use Xvlvv\Services\Application\AuthService;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';
    private ?User $_user = null;
    private AuthService $authService;

    public function __construct(AuthService $authService, $config = [])
    {
        $this->authService = $authService;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'safe'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Валидирует пароль, используя AuthService
     * @param string $attribute
     * @param $params
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
     * Возвращает найденного пользователя после успешной валидации
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