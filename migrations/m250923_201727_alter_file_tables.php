<?php

use yii\db\Migration;

class m250923_201727_alter_file_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->dropColumn('{{%file}}', 'size_bytes');
        $this->addColumn('{{%file}}', 'task_id', $this->integer()->notNull()->unsigned());
        $this->addForeignKey(
            'fk-file-task_id',
            '{{%file}}',
            'task_id',
            '{{%task}}',
            'id',
            'CASCADE'
        );
        $this->dropTable('{{%task_file}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->addColumn('{{%file}}', 'size_bytes', $this->integer()->notNull()->unsigned());
        $this->dropColumn('{{%file}}', 'task_id');
        $this->dropForeignKey('fk-file-task_id', '{{%file}}');
        $this->createTable(
            '{{%task_file}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'task_id' => $this->integer()->notNull()->unsigned(),
                'file_id' => $this->integer()->notNull()->unsigned(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append(
                    'ON UPDATE CURRENT_TIMESTAMP'
                ),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci'
        );
    }
}
