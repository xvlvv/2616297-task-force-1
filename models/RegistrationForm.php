<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель формы для регистрации нового пользователя.
 * Поддерживает два сценария: стандартная регистрация и регистрация через VK.
 */
class RegistrationForm extends Model
{
    public const string SCENARIO_DEFAULT_REGISTER = 'default_register';
    public const string SCENARIO_VK_REGISTER = 'vk_register';

    /** @var string Имя пользователя */
    public string $name = '';

    /** @var string Email пользователя */
    public string $email = '';

    /** @var int ID города пользователя */
    public int $cityId = 0;

    /** @var array Список городов для выпадающего списка */
    public array $cities = [];

    /** @var string Пароль */
    public string $password = '';

    /** @var string Повтор пароля */
    public string $passwordRepeat = '';

    /** @var string Название города (устаревшее, используется cityId) */
    public string $city = '';

    /** @var bool Флаг, является ли пользователь исполнителем */
    public bool $isWorker = false;

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT_REGISTER] = ['name', 'email', 'cityId', 'password', 'passwordRepeat', 'isWorker'];
        $scenarios[self::SCENARIO_VK_REGISTER] = ['name', 'email', 'cityId', 'isWorker'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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