<?php

/**
 * the user information retrieved from the db without using the session
 * 
 * Before using this class ALWAYS eveluate isGuest!!!!
 * 
 * version 1.0 jvk 2012-09-13
 * ref: http://www.yiiframework.com/wiki/60/
 * 
 * to install:
 * 'components'=>array(
 *   'user'=>array(
 *       'class' => 'WebUser',
 *       ),
 * ),
 */

class WebUser extends CWebUser
{
	
	private $_profile;
	
	public function getProfile()
	{
		if ($this->_profile == null) {
			$this->_profile = UserProfile::model()->findByPk(Yii::App()->user->id);
			if ($this->_profile == null) {
				$this->_profile = new UserProfile;
				$this->_profile->username = 'Guest';
				$this->_profile->id = 1;
				$this->_profile->rights_id = UserProfile::GUEST;
			}	
		}
		return $this->_profile;
	}
	
	public function getIsAdmin()
	{
		return $this->profile->rights_id >= UserProfile::ADMINISTRATOR;
	}
	public function getIsModerator()
	{
		return $this->profile->rights_id >= UserProfile::MODERATOR;
	}
	public function getIsCustomer()
	{
		return $this->profile->rights_id >= UserProfile::PAYING_CUSTOMER;
	}
	public function getIsRegisterd()
	{
		return $this->profile->rights_id >= UserProfile::REGISTERED_USER;
	}
	public function getRights()
	{
		return $this->profile->rights_id;
	}
	public function getCanEdit()
	{
		return $this->profile->canEdit;
	}
}
?>
