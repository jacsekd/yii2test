<?php

use yii\db\Migration;

class m190925_094200_create_images_table extends Migration
{
    public function up()
    {
        $this->createTable('images', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'file_path' => $this->string(),
            'extension' => $this->string()
        ]);
        $this->createIndex(
            'idx-images-ticket_id',
            'images',
            'ticket_id'
        );
        $this->addForeignKey(
            'fk-images-ticket_id',
            'images',
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
            'fk-images-ticket_id',
            'images'
        );

        $this->dropIndex(
            'idx-images-ticket_id',
            'images'
        );

        $this->dropTable('comment');
    }
}
