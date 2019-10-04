<?php

use yii\db\Migration;

class m190911_172500_create_comment_table extends Migration
{
    public function up()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'text' => $this->string()
        ]);
        $this->createIndex(
            'idx-comment-author_id',
            'comment',
            'author_id'
        );
        $this->addForeignKey(
            'fk-comment-author_id',
            'comment',
            'author_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex(
            'idx-comment-ticket_id',
            'comment',
            'ticket_id'
        );
        $this->addForeignKey(
            'fk-comment-ticket_id',
            'comment',
            'ticket_id',
            'ticket',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {

        $this->dropForeignKey(
            'fk-comment-author_id',
            'comment'
        );

        $this->dropIndex(
            'idx-comment-author_id',
            'comment'
        );

        $this->dropForeignKey(
            'fk-comment-ticket_id',
            'comment'
        );

        $this->dropIndex(
            'idx-comment-ticket_id',
            'comment'
        );

        $this->dropTable('comment');
    }
}
