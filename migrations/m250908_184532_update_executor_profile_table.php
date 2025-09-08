<?php

use yii\db\Migration;

class m250908_184532_update_executor_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(
            '{{%executor_profile}}',
            'show_contacts_only_to_customer',
            'restrict_contacts'
        );

        $this->dropColumn('{{%executor_profile}}', 'failed_tasks_count');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn(
            '{{%executor_profile}}',
            'failed_tasks_count',
            'unsigned int NOT NULL DEFAULT 0'
        );

        $this->renameColumn(
            '{{%executor_profile}}',
            'restrict_contacts',
            'show_contacts_only_to_customer'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250908_184532_update_executor_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
