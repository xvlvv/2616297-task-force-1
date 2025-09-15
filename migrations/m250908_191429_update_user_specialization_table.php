<?php

use yii\db\Migration;

class m250908_191429_update_user_specialization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex(
            'uk_user_category',
            '{{%user_specialization}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250908_191429_update_user_specialization_table cannot be reverted.\n";

        return false;
    }
    */
}
