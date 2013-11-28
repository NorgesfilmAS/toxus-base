<?php

class m130406_150535_user_profile extends CDbMigration
{
	private $_dbOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

	public function up()
	{
		$this->createTable('user_profile', array(
			'id' => 'pk',
			'username' => 'string not null',
			'password' => 'string not null',
			'password_md5' => 'string',
			'login_key' => 'string',				
			'email' => 'string not null',
			'email_to_confirm' => 'string',	
			'is_confirmed' => 'boolean default 0',	
			'rights_id' => 'integer default 1 not null',					
			'creation_date' => 'datetime',
			'modified_date' => 'datetime',
			'last_login' => 'datetime',	
			'has_newsletter' => 'boolean default false not null',	
		), $this->_dbOptions);		
		$this->createIndex('ix_user_profile_unique', 'user_profile', 'email', true);
		
		// create the admin profile
		$this->execute(
				"INSERT INTO `user_profile` (`id`, `username`, `password`, `password_md5`, `login_key`, `email`, `email_to_confirm`, `is_confirmed`, `rights_id`, `creation_date`, `modified_date`, `last_login`, `has_newsletter`)
				VALUES(1, 'admin', '2bad4u', '3c1657a7f838207db6495c19b61a1973', NULL, 'info@toxus.nl', NULL, 1, 1, NULL, NULL, NULL, 0);
		");
		
		$this->createTable('mail', array(
			'id' => 'pk',
			'profile_id' => 'int default 0 not null',
			'to' => 'string',	
			'creation_date' => 'datetime',
			'message' => 'text',
			'log' => 'text'	
		), $this->_dbOptions);
		$this->createIndex('ix_mail_profile_id', 'mail', 'profile_id');
	}

	public function down()
	{
		$this->dropTable('mail');
		$this->dropTable('user_profile');
	}
}
