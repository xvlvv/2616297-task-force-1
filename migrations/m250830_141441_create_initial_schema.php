<?php

use yii\db\Migration;

class m250830_141441_create_initial_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        $isMySql = $this->db->driverName === 'mysql';

        if ($isMySql) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        }

        $timestampColumns = [
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append(
                'ON UPDATE CURRENT_TIMESTAMP'
            ),
        ];

        $this->createTable(
            '{{%city}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'name' => $this->string(128)->notNull(),
                    'latitude' => $this->decimal(10, 8)->notNull(),
                    'longitude' => $this->decimal(11, 8)->notNull(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createTable(
            '{{%category}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'name' => $this->string(128)->notNull(),
                    'icon' => $this->string(255),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createTable(
            '{{%user}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'name' => $this->string(255)->notNull(),
                    'email' => $this->string(255)->notNull()->unique(),
                    'password_hash' => $this->string(255),
                    'role' => $this->string(255)->notNull(),
                    'city_id' => $this->integer()->notNull()->unsigned(),
                    'avatar_path' => $this->string(255),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-user-city_id',
            '{{%user}}',
            'city_id'
        );


        $this->addForeignKey(
            'fk-user-city_id',
            '{{%user}}',
            'city_id',
            '{{%city}}',
            'id'
        );

        $this->createTable(
            '{{%executor_profile}}',
            array_merge(
                [
                    'user_id' => $this->primaryKey()->unsigned(),
                    'day_of_birth' => $this->date(),
                    'bio' => $this->text(),
                    'phone_number' => $this->char(11),
                    'telegram_username' => $this->string(64),
                    'failed_tasks_count' => $this->integer()->defaultValue(0)->notNull()->unsigned(),
                    'show_contacts_only_to_customer' => $this->boolean()->defaultValue(false),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->addForeignKey(
            'fk-executor_profile-user_id',
            '{{%executor_profile}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createTable(
            '{{%customer_profile}}',
            array_merge(
                [
                    'user_id' => $this->primaryKey()->unsigned(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->addForeignKey(
            'fk-customer_profile-user_id',
            '{{%customer_profile}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createTable(
            '{{%user_specialization}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'user_id' => $this->integer()->notNull()->unsigned(),
                    'category_id' => $this->integer()->notNull()->unsigned(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-user_specialization-user_id',
            '{{%user_specialization}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-user_specialization-user_id',
            '{{%user_specialization}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-user_specialization-category_id',
            '{{%user_specialization}}',
            'category_id',
        );

        $this->addForeignKey(
            'fk-user_specialization-category_id',
            '{{%user_specialization}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'uk_user_category',
            '{{%user_specialization}}',
            [
                'user_id',
                'category_id',
            ],
            true
        );

        $this->createTable(
            '{{%file}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'original_name' => $this->string(255)->notNull(),
                    'path' => $this->string(255)->notNull()->unique(),
                    'mime_type' => $isMySql ? "ENUM('image/jpeg', 'image/png', 'image/webp')" : $this->string(
                        128
                    )->notNull(),
                    'size_bytes' => $this->integer()->notNull()->unsigned(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createTable(
            '{{%task}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'name' => $this->string(255)->notNull(),
                    'description' => $this->text()->notNull(),
                    'category_id' => $this->integer()->notNull()->unsigned(),
                    'customer_id' => $this->integer()->notNull()->unsigned(),
                    'worker_id' => $this->integer()->unsigned(),
                    'city_id' => $this->integer()->unsigned(),
                    'status' => $this->string(32)->notNull()->defaultValue('new'),
                    'budget' => $this->integer()->unsigned(),
                    'latitude' => $this->decimal(10, 8),
                    'longitude' => $this->decimal(11, 8),
                    'end_date' => $this->timestamp(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-task-category_id',
            '{{%task}}',
            'category_id'
        );

        $this->addForeignKey(
            'fk-task-category_id',
            '{{%task}}',
            'category_id',
            '{{%category}}',
            'id'
        );

        $this->createIndex(
            'idx-task-customer_id',
            '{{%task}}',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-task-customer_id',
            '{{%task}}',
            'customer_id',
            '{{%user}}',
            'id'
        );

        $this->createIndex(
            'idx-task-worker_id',
            '{{%task}}',
            'worker_id'
        );

        $this->addForeignKey(
            'fk-task-worker_id',
            '{{%task}}',
            'worker_id',
            '{{%user}}',
            'id'
        );

        $this->createIndex(
            'idx-task-city_id',
            '{{%task}}',
            'city_id'
        );

        $this->addForeignKey(
            'fk-task-city_id',
            '{{%task}}',
            'city_id',
            '{{%city}}',
            'id'
        );

        $this->createIndex(
            'idx-task_status_created_at',
            '{{%task}}',
            [
                'status',
                'created_at',
            ]
        );

        $this->createIndex(
            'idx-task_customer_id_status',
            '{{%task}}',
            [
                'customer_id',
                'status',
            ]
        );

        $this->createIndex(
            'idx-task_worker_id_status',
            '{{%task}}',
            [
                'worker_id',
                'status',
            ]
        );

        if ($isMySql) {
            $this->execute('CREATE FULLTEXT INDEX ft_task_name_description ON {{%task}} (name, description)');
        }

        $this->createTable(
            '{{%task_file}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'task_id' => $this->integer()->notNull()->unsigned(),
                    'file_id' => $this->integer()->notNull()->unsigned(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-task_file-task_id',
            '{{%task_file}}',
            'task_id'
        );

        $this->addForeignKey(
            'fk-task_file-task_id',
            '{{%task_file}}',
            'task_id',
            '{{%task}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-task_file-file_id',
            '{{%task_file}}',
            'file_id'
        );

        $this->addForeignKey(
            'fk-task_file-file_id',
            '{{%task_file}}',
            'file_id',
            '{{%file}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'uk_task_file',
            '{{%task_file}}',
            [
                'task_id',
                'file_id',
            ],
            true
        );

        $this->createTable(
            '{{%task_response}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'task_id' => $this->integer()->notNull()->unsigned(),
                    'worker_id' => $this->integer()->notNull()->unsigned(),
                    'comment' => $this->text(),
                    'price' => $this->integer()->unsigned(),
                    'is_rejected' => $this->boolean()->notNull()->defaultValue(false),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-task_response_worker_id_task_id',
            '{{%task_response}}',
            [
                'worker_id',
                'task_id',
            ]
        );

        $this->createIndex(
            'idx-task_response-task_id',
            '{{%task_response}}',
            'task_id'
        );

        $this->addForeignKey(
            'fk-task_response-task_id',
            '{{%task_response}}',
            'task_id',
            '{{%task}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-task_response-worker_id',
            '{{%task_response}}',
            'worker_id'
        );

        $this->addForeignKey(
            'fk-task_response-worker_id',
            '{{%task_response}}',
            'worker_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createTable(
            '{{%review}}',
            array_merge(
                [
                    'id' => $this->primaryKey()->unsigned(),
                    'task_id' => $this->integer()->notNull()->unique()->unsigned(),
                    'customer_id' => $this->integer()->notNull()->unsigned(),
                    'worker_id' => $this->integer()->notNull()->unsigned(),
                    'comment' => $this->text()->notNull(),
                    'rating' => $this->tinyInteger()->notNull()->unsigned(),
                ],
                $timestampColumns
            ),
            $tableOptions
        );

        $this->createIndex(
            'idx-review-task_id',
            '{{%review}}',
            'task_id'
        );

        $this->addForeignKey(
            'fk-review-task_id',
            '{{%review}}',
            'task_id',
            '{{%task}}',
            'id'
        );

        $this->createIndex(
            'idx-review-customer_id',
            '{{%review}}',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-review-customer_id',
            '{{%review}}',
            'customer_id',
            '{{%user}}',
            'id'
        );

        $this->createIndex(
            'idx-review-worker_id',
            '{{%review}}',
            'worker_id'
        );

        $this->addForeignKey(
            'fk-review-worker_id',
            '{{%review}}',
            'worker_id',
            '{{%user}}',
            'id'
        );

        $this->addCheck(
            'chk_rating_range',
            '{{%review}}',
            'rating BETWEEN 1 AND 5'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $isMySql = $this->db->driverName === 'mysql';

        $this->dropCheck('chk_rating_range', '{{%review}}');
        $this->dropForeignKey('fk-review-worker_id', '{{%review}}');
        $this->dropIndex('idx-review-worker_id', '{{%review}}');
        $this->dropForeignKey('fk-review-customer_id', '{{%review}}');
        $this->dropIndex('idx-review-customer_id', '{{%review}}');
        $this->dropForeignKey('fk-review-task_id', '{{%review}}');
        $this->dropIndex('idx-review-task_id', '{{%review}}');
        $this->dropTable('{{%review}}');

        $this->dropForeignKey('fk-task_response-worker_id', '{{%task_response}}');
        $this->dropIndex('idx-task_response-worker_id', '{{%task_response}}');
        $this->dropForeignKey('fk-task_response-task_id', '{{%task_response}}');
        $this->dropIndex('idx-task_response-task_id', '{{%task_response}}');
        $this->dropIndex('idx-task_response_worker_id_task_id', '{{%task_response}}');
        $this->dropTable('{{%task_response}}');

        $this->dropIndex('uk_task_file', '{{%task_file}}');
        $this->dropForeignKey('fk-task_file-file_id', '{{%task_file}}');
        $this->dropIndex('idx-task_file-file_id', '{{%task_file}}');
        $this->dropForeignKey('fk-task_file-task_id', '{{%task_file}}');
        $this->dropIndex('idx-task_file-task_id', '{{%task_file}}');
        $this->dropTable('{{%task_file}}');

        if ($isMySql) {
            $this->execute('ALTER TABLE {{%task}} DROP INDEX ft_task_name_description');
        }

        $this->dropIndex('idx-task_worker_id_status', '{{%task}}');
        $this->dropIndex('idx-task_customer_id_status', '{{%task}}');
        $this->dropIndex('idx-task_status_created_at', '{{%task}}');
        $this->dropForeignKey('fk-task-city_id', '{{%task}}');
        $this->dropIndex('idx-task-city_id', '{{%task}}');
        $this->dropForeignKey('fk-task-worker_id', '{{%task}}');
        $this->dropIndex('idx-task-worker_id', '{{%task}}');
        $this->dropForeignKey('fk-task-customer_id', '{{%task}}');
        $this->dropIndex('idx-task-customer_id', '{{%task}}');
        $this->dropForeignKey('fk-task-category_id', '{{%task}}');
        $this->dropIndex('idx-task-category_id', '{{%task}}');
        $this->dropTable('{{%task}}');

        $this->dropTable('{{%file}}');

        $this->dropIndex('uk_user_category', '{{%user_specialization}}');
        $this->dropForeignKey('fk-user_specialization-category_id', '{{%user_specialization}}');
        $this->dropIndex('idx-user_specialization-category_id', '{{%user_specialization}}');
        $this->dropForeignKey('fk-user_specialization-user_id', '{{%user_specialization}}');
        $this->dropIndex('idx-user_specialization-user_id', '{{%user_specialization}}');
        $this->dropTable('{{%user_specialization}}');

        $this->dropForeignKey('fk-customer_profile-user_id', '{{%customer_profile}}');
        $this->dropTable('{{%customer_profile}}');

        $this->dropForeignKey('fk-executor_profile-user_id', '{{%executor_profile}}');
        $this->dropTable('{{%executor_profile}}');

        $this->dropForeignKey('fk-user-city_id', '{{%user}}');
        $this->dropIndex('idx-user-city_id', '{{%user}}');
        $this->dropTable('{{%user}}');

        $this->dropTable('{{%category}}');
        $this->dropTable('{{%city}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250830_141441_create_initial_schema cannot be reverted.\n";

        return false;
    }
    */
}
