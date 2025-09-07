<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%executor_profile}}".
 *
 * @property int $user_id
 * @property string|null $day_of_birth
 * @property string|null $bio
 * @property string|null $phone_number
 * @property string|null $telegram_username
 * @property int $failed_tasks_count
 * @property int|null $show_contacts_only_to_customer
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $user
 */
class ExecutorProfile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%executor_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day_of_birth', 'bio', 'phone_number', 'telegram_username'], 'default', 'value' => null],
            [['show_contacts_only_to_customer'], 'default', 'value' => 0],
            [['day_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['bio'], 'string'],
            [['show_contacts_only_to_customer'], 'boolean'],
            [['failed_tasks_count'], 'integer'],
            [['phone_number'], 'string', 'max' => 11],
            [['telegram_username'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID пользователя',
            'day_of_birth' => 'День рождения',
            'bio' => 'О себе',
            'phone_number' => 'Номер телефона',
            'telegram_username' => '@ в Telegram',
            'failed_tasks_count' => 'Количество проваленных заданий',
            'show_contacts_only_to_customer' => 'Показывать контакты только клиенту',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
