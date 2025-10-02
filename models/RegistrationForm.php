<?php

namespace app\models;

use yii\base\Model;

class RegistrationForm extends Model
{
    public const SCENARIO_DEFAULT_REGISTER = 'default_register';
    public const SCENARIO_VK_REGISTER = 'vk_register';

    public string $name = '';
    public string $email = '';
    public int $cityId = 0;
    public array $cities = [];
    public string $password = '';
    public string $passwordRepeat = '';
    public string $city = '';
    public bool $isWorker = false;

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT_REGISTER] = ['name', 'email', 'cityId', 'password', 'passwordRepeat', 'isWorker'];
        $scenarios[self::SCENARIO_VK_REGISTER] = ['name', 'email', 'cityId', 'isWorker'];
        return $scenarios;
    }

    public function rules(): array
    {
        return [
            ['name', 'required', 'message' => 'Введите имя'],
            ['email', 'required', 'message' => 'Введите адрес электронной почты'],
            [['password', 'passwordRepeat'], 'required', 'message' => 'Введите пароль', 'on' => self::SCENARIO_DEFAULT_REGISTER],
            ['cityId', 'required', 'message' => 'Укажите ваш город'],
            ['password', 'compare', 'compareAttribute' => 'passwordRepeat', 'message' => 'Пароли не совпадают', 'on' => self::SCENARIO_DEFAULT_REGISTER],
            ['passwordRepeat', 'safe'],
            [
                'cityId',
                'exist',
                'targetClass' => City::class,
                'targetAttribute' => ['cityId' => 'id'],
                'message' => 'Города не существует'
            ],
            ['email', 'email', 'message' => 'Введите правильный адрес электронной почты'],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'email',
                'message' => 'Адрес электронной почты уже существует'
            ],
            ['isWorker', 'boolean', 'message' => 'Укажите правильную роль'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Ваше имя',
            'email' => 'Email',
            'cityId' => 'Город',
            'password' => 'Пароль',
            'passwordRepeat' => 'Повтор пароля',
            'isWorker' => 'я собираюсь откликаться на заказы',
        ];
    }
}