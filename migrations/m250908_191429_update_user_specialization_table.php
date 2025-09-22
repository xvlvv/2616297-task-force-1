<?php

use yii\db\Migration;

class m250908_191429_update_user_specialization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->dropIndex(
            'uk_user_category',
            '{{%user_specialization}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->createIndex(
            'uk_user_category',
            '{{%user_specialization}}',
            [
                'user_id',
                'category_id',
            ],
            true
        );
    }
}
