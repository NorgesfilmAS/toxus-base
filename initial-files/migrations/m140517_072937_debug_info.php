<?php

class m140517_072937_debug_info extends CDbMigration
{
	private $_dbOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
	
	public function up()
	{
		$this->createTable('debug_info', array(
			'id' => 'pk',
			'creation_date' => 'datetime',								// when
			'app_ident' => 'string',											// which application
			'device_ident' => 'string',										// on which device
			'session_ident' => 'string',									// session used for this errpr				
			'user_id' => 'integer',												// can be read from the session but more easy		
			'type_id' => 'tinyint default 0',							// 0: exception or 1: error, 2:warning, 3:log, 4:info 				
			'error_url' => 'string',											// url called that caused the error
			'error_message' => 'text',										// message for the exception	
			'cause' => 'string',													// alway ''	
			'stacktrace' => 'text',												// json formated stacktrace	
			'state' => 'text',														// other variables recieved	
		), $this->_dbOptions);
		$this->createIndex('ix_debug_info_app', 'debug_info', 'app_ident,creation_date');
		$this->createIndex('ix_debug_info_user', 'debug_info', 'user_id, session_ident');
		
		// create user specific tracing for error,warn, etc. Exception are always logged
		$this->addColumn('user_profile', 'trace_level', 'integer default 0');		
			// 0: Exception, 1: Error, 2: Warn, 3: Info, 4: Log, -1: turn all off
	}

	public function down()
	{
		$this->dropColumn('user_profile', 'trace_level');
		$this->dropTable('debug_info');
		return true;
	}
}