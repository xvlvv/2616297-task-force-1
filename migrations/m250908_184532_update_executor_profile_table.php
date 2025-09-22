<?php

use yii\db\Migration;

class m250908_184532_update_executor_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
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
    public function safeDown(): void
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
}
