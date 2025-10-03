<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord модель для таблицы "{{%customer_profile}}".
 * Представляет профиль пользователя с ролью "Заказчик".
 *
 * @property int $user_id ID пользователя, к которому привязан профиль
 * @property string|null $created_at Время создания
 * @property string|null $updated_at Время последнего обновления
 *
 * @property User $user Связанная модель пользователя
 */
class CustomerProfile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%customer_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => 'ID пользователя',
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
