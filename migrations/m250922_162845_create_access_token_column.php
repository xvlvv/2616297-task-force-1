<?php

use yii\db\Migration;

class m250922_162845_create_access_token_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%user}}', 'access_token', $this->string()->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{%user}}', 'access_token');
    }
}
