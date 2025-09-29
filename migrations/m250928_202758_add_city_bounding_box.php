<?php

use yii\db\Migration;

class m250928_202758_add_city_bounding_box extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%city}}', 'bounding_box', $this->string());
        $this->addColumn('{{%task}}', 'location_info', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{%task}}', 'location_info');
        $this->dropColumn('{{%city}}', 'bounding_box');
    }
}
