<?php

namespace app\models;

use yii\base\Model;

class RegistrationForm extends Model
{
    public string $name = '';
    public string $email = '';
    public int $cityId = 0;
    public array $cities = [];
    public string $password = '';
    public string $passwordRepeat = '';
    public string $city = '';
    public bool $isWorker = false;

    public function rules(): array
    {
        return [
            ['name', 'required', 'message' => 'Введите имя'],
            ['email', 'required', 'message' => 'Введите адрес электронной почты'],
            ['password', 'required', 'message' => 'Введите пароль'],
            ['cityId', 'required', 'message' => 'Укажите ваш город'],
            ['password', 'compare', 'compareAttribute' => 'passwordRepeat', 'message' => 'Пароли не совпадают'],
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