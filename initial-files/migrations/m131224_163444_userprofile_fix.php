<?php

class m131224_163444_userprofile_fix extends CDbMigration
{
private $_dbOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

	public function up()
	{
		$this->addColumn('user_profile', 'is_suspended', 'integer');
	}
	public function down()
	{
		$this->dropColumn('user_profile', 'is_suspended');
	}
}