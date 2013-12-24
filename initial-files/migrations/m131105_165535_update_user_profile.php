<?php

class m131105_165535_update_user_profile extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('user_profile', 'email', 'string');
		$this->createIndex('ix-user-profile-username', 'user_profile', 'username', true);
	}

	public function down()
	{
		$this->alterColumn('user_profile', 'email', 'string not null');
		$this->dropIndex('ix-user-profile-username', 'user_profile');
	}

}