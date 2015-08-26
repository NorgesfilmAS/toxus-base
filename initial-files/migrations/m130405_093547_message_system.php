<?php

class m130405_093547_message_system extends CDbMigration
{
	private $_dbOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

	public function up()
	{
		$this->createTable('message', array(
				'id' => 'pk',	
				'name' => 'string not null',
				'email' => 'string not null',
				'subject' => 'string',
				'message' => 'text not null',
				'creation_date' => 'datetime',
				'modified_date' => 'datetime',
				'user_id' => 'integer default 0 not null',
				'ip' => 'string',
				'is_handled' => 'boolean default false',
				'reply_to_message_id' => 'integer default 0 not null',
			), $this->_dbOptions);
	}

	public function down()
	{
		$this->dropTable('message');
	}

}