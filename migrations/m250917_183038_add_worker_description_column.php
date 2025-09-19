<?php

use yii\db\Migration;

class m250917_183038_add_worker_description_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%executor_profile}}',
            'description',
            $this->text()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            '{{%executor_profile}}',
            'description'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250917_183038_add_worker_description_column cannot be reverted.\n";

        return false;
    }
    */
}
