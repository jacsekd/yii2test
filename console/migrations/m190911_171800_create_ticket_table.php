<?php

use yii\db\Migration;

class m190911_171800_create_ticket_table extends Migration
{
	public function up()
	{
		$this->createTable('ticket', [
			'id' => $this->primaryKey(),
			'title' => $this->string(),
			'status' => $this->boolean(),
			'author_id' => $this->integer()->notNull(),
			'created_at' => $this->integer(),
			'last_comment_time' => $this->integer(),
			'admin_id' => $this->integer(),
			'updated_at' => $this->integer(),
			'text' => $this->string(65000),
			'secret_id' => $this->string()
		]);

		$this->createIndex(
			'idx-ticket-author_id',
			'ticket',
			'author_id'
        	);

		$this->addForeignKey(
			'fk-ticket-author_id',
			'ticket',
			'author_id',
			'user',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->createIndex(
			'idx-ticket-admin_id',
			'ticket',
			'admin_id'
        	);

		$this->addForeignKey(
			'fk-ticket-admin_id',
			'ticket',
			'admin_id',
			'user',
			'id',
			'SET NULL',
			'CASCADE'
		);
	}
	public function down()
	{
		$this->dropForeignKey(
			'fk-ticket-author_id',
			'ticket'
		);

		$this->dropIndex(
			'idx-ticket-author_id',
			'ticket'
		);

		$this->dropForeignKey(
			'fk-ticket-admin_id',
			'ticket'
		);

		$this->dropIndex(
			'idx-ticket-admin_id',
			'ticket'
		);
		
		$this->dropTable('ticket');
	}
	}
