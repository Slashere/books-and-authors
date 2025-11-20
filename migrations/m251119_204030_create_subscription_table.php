<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m251119_204030_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx_subscription_author_phone',
            '{{%subscription}}',
            ['author_id', 'phone'],
            true
        );

        $this->addForeignKey(
            'fk_subscription_author',
            '{{%subscription}}', 'author_id',
            '{{%author}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscription}}');
    }
}
