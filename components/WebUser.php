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
	
	const LAST_INSERT_ID = 'lastInsertId';
	
	protected $_profile;
	
	public function getProfile()
	{
		if ($this->_profile == null) {
			$this->_profile = UserProfile::model()->findByPk(Yii::App()->user->id);
			if ($this->_profile == null) {
				$this->_profile = new UserProfile;
				$this->_profile->username = 'Guest';
				$this->_profile->id = 1;
				$this->_profile->rights_id = 1;// UserProfile::GUEST;
			}	
		}
		return $this->_profile;
	}
	
	public function init() {
		parent::init();
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
	
	
	public function getLastInsertId()
	{
		if ($this->hasState(self::LAST_INSERT_ID))
			return $this->getState (self::LAST_INSERT_ID);
		return null;
	}	
	public function setLastInsertid($value)
	{
		$this->setState(self::LAST_INSERT_ID, $value);
	}
	public function clearLastInsert()
	{
		$this->setState(self::LAST_INSERT_ID, null);
	}
	
	/**
	 * returns the lastInsertId and removes the value
	 * @return integer
	 */
	public function getLastId()
	{
		$id = $this->lastInsertId;
		$this->clearLastInsert();
		return $id;
	}
	
}
?>
