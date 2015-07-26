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
	public $userProfileClass = 'UserProfileModel';
	/**
	 *
	 * @var type if set to true the is isGuest alway evaluate to true
	 */
	public $allowAll = false;
  
	const LAST_INSERT_ID = 'lastInsertId';
	
	protected $_profile = null;
	
	public function getProfile()
	{
		if ($this->_profile === null) {
      $class = $this->userProfileClass;
			try {
				$this->_profile = $class::model()->findByPk(Yii::App()->user->id);
				if ($this->_profile == null) {
					$this->_profile = new UserProfile;
					$this->_profile->username = 'Guest';
					$this->_profile->id = 1;
					$this->_profile->rights_id = 1;// UserProfile::GUEST;
				}	
			} catch(Exception  $e) {
				Yii::log('Error opening db: '.$e->getMessage(), CLogger::LEVEL_WARNING, 'toxus.WebUser.getProfile');
				$this->_profile = false;
			}	
		}
		return $this->_profile;
	}
	public function getIsGuest()
	{
		
		return $this->allowAll || parent::getIsGuest();
	}
	
	public function init() {
		parent::init();
	}
	
	public function getIsAdmin()
	{
    $class = $this->userProfileClass;
		return $this->profile->rights_id >= $class::ADMINISTRATOR;
	}
	public function getIsModerator()
	{
    $class = $this->userProfileClass;
		return $this->profile->rights_id >= $class::MODERATOR;
	}
	public function getIsCustomer()
	{
    $class = $this->userProfileClass;
		return $this->profile->rights_id >= $class::PAYING_CUSTOMER;
	}
	public function getIsRegisterd()
	{
    $class = $this->userProfileClass;
		return $this->profile->rights_id >= $class::REGISTERED_USER;
	}

  /**
   * return true if the user can update the help system
   * 
   * @return true|false
   */
  public function getCanUpdateHelp()
  {
    return true;
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
	
	public function hasMenu($key)
	{
		return true;
	}
}
?>
