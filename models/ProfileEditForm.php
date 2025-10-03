<?php

namespace app\models;

use Xvlvv\Entity\WorkerProfile;
use Xvlvv\Repository\UserRepositoryInterface;
use Yii;
use Xvlvv\Entity\User;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Модель формы для редактирования профиля пользователя-исполнителя.
 */
class ProfileEditForm extends Model
{
    /** @var string Имя пользователя */
    public string $name = '';

    /** @var string Email пользователя */
    public string $email = '';

    /** @var string|null Дата рождения */
    public ?string $birthday = null;

    /** @var string|null Номер телефона */
    public ?string $phone = null;

    /** @var string|null Имя пользователя в Telegram */
    public ?string $telegram = null;

    /** @var string|null Информация "О себе" */
    public ?string $bio = null;

    /** @var string|array Массив ID выбранных специализаций */
    public string|array $specializations = [];

    /** @var UploadedFile|null|string Загруженный файл аватара */
    public null|string|UploadedFile $avatarFile = '';

    /** @var User Доменная сущность текущего пользователя */
    private User $_user;

    /**
     * @param User $user Доменная сущность пользователя для инициализации формы
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
        $this->loadFromEntity($user);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email'],
            ['email', 'validateUniqueEmail'],
            [['birthday', 'phone', 'telegram', 'bio'], 'string'],
            [['specializations'], 'each', 'rule' => ['integer']],
            [['avatarFile'], 'image', 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5],
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
            'birthday' => 'День рождения',
            'phone' => 'Номер телефона',
            'telegram' => 'Telegram',
            'bio' => 'Информация о себе',
            'specializations' => 'Выбор специализаций',
            'avatarFile' => 'Аватар',
        ];
    }

    /**
     * Кастомный валидатор для проверки уникальности email.
     * Проверяет, не занят ли email другим пользователем.
     *
     * @param string $attribute Атрибут для валидации
     * @param array|null $params Дополнительные параметры
     */
    public function validateUniqueEmail(string $attribute, ?array $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = Yii::$container->get(UserRepositoryInterface::class)->getByEmail($this->email);

        if ($user && $user->getId() !== $this->_user->getId()) {
            $this->addError($attribute, 'Этот email уже занят.');
        }
    }

    private function loadFromEntity(User $user): void
    {
        $this->name = $user->getName();
        $this->email = $user->getEmail();

        /** @var WorkerProfile $profile */
        $profile = $user->getProfile();
        $this->birthday = $profile->getDayOfBirth();
        $this->phone = $profile->getPhoneNumber();
        $this->telegram = $profile->getTelegramUsername();
        $this->bio = $profile->getBio();

        $this->specializations = $profile->getSpecializationsIds();
    }
}