<?php

class m130408_063203_logging extends CDbMigration
{
	private $_dbOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

	public function up()
	{
		$this->createTable('logging', array(
			'id' => 'pk',
			'creation_date' => 'datetime',
			'profile_id' => 'integer default 0',
			'controller' => 'string',										  /* which controller is used */	
			'model_id' => 'integer default 0 not null',	  /* the id we did work on */
			'processing_time' => 'string default 0 not null',	
			'message' => 'text',													/* message logged */	
				
			'url' => 'string',
			'referer' => 'string',
			'ip' => 'string',
			'error_state' => 'text',	
		), $this->_dbOptions);
		$this->createIndex('ix_logging_create', 'logging', 'creation_date');
		$this->createIndex('ix_logging_profile', 'logging', 'profile_id');
		
	}

	public function down()
	{
		$this->dropTable('logging');
	}
}