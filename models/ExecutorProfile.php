<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%executor_profile}}".
 * Представляет профиль пользователя с ролью "Исполнитель".
 *
 * @property int $user_id ID пользователя
 * @property string|null $day_of_birth Дата рождения
 * @property string|null $bio Информация "О себе"
 * @property string|null $phone_number Номер телефона
 * @property string|null $telegram_username Имя пользователя в Telegram
 * @property bool|null $restrict_contacts Флаг "Показывать контакты только заказчику"
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property User $user Связанная модель пользователя
 */
class ExecutorProfile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%executor_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['day_of_birth', 'bio', 'phone_number', 'telegram_username'], 'default', 'value' => null],
            [['restrict_contacts'], 'default', 'value' => 0],
            [['day_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['bio'], 'string'],
            [['restrict_contacts'], 'boolean'],
            [['phone_number'], 'string', 'max' => 11],
            [['telegram_username'], 'string', 'max' => 64],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => 'ID пользователя',
            'day_of_birth' => 'День рождения',
            'bio' => 'О себе',
            'phone_number' => 'Номер телефона',
            'telegram_username' => '@ в Telegram',
            'restrict_contacts' => 'Показывать контакты только клиенту',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Определяет связь с моделью User
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
