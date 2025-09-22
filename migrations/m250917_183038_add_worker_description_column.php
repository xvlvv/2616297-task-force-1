<?php

use yii\db\Migration;

class m250917_183038_add_worker_description_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
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
    public function safeDown(): void
    {
        $this->dropColumn(
            '{{%executor_profile}}',
            'description'
        );
    }
}
