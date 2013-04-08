<?php
/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginFormModel extends CFormModel
{
	public $id;
	private $_username;
	private $_email;
	private $_password;
	private $_passwordRepeat;
	private $_rememberMe;
	private $_hasNewsletter;

	private $_identity;

	
	public function getUsername()
	{
		return $this->_username;
	}
	public function getPassword()
	{
		return $this->_password;
	}
	public function getRememberMe()
	{
		return $this->_rememberMe;
	}
	public function setRememberMe($value)
	{
		$this->_rememberMe = $value;
	}
	public function setUsername($value)
	{
		$this->_username = $value;
	}
	public function setPassword($value)
	{
		$this->_password = $value;
	}
	public function getEmail()
	{
		return $this->_email;
	}
	public function setEmail($value)
	{
		$this->_email = $value;
	}
	public function getHas_newsletter()
	{
		return $this->_hasNewsletter;
	}
	public function setHas_newsletter($value)
	{
		return $this->_hasNewsletter;
	}	
	public function getIsNewRecord()
	{
		return true;
	}
	public function getPasswordRepeat()
	{
		return $this->_passwordRepeat;
	}
	public function setPasswordRepeat($value)
	{
		$this->_passwordRepeat = $value;
	}
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('email,username', 'length', 'max' => 250, 'min' => 2),				
			// username and password are required
			array('username, password', 'required', 'on' => 'login'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean', 'on' => 'login'),
			// password needs to be authenticated
			array('password', 'authenticate', 'on' => 'login'),
			// email when requesting passwor
			array('email', 'email', 'on' => 'password'),	
			array('email', 'required', 'on' => 'password'),		
			
			array('username, password,passwordRepeat', 'required', 'on' => 'new'),	
			array('email', 'email', 'on' => 'login'),		
			array('email', 'emailIsUnique','on' => 'new'),				
			array('has_newsletter', 'boolean', 'on'=>'new'),	
			array('password', 'length', 'min'=> 5, 'max' => 40, 'on'=>'new'),	
			array('password', 'compare', 'compareAttribute'=>'passwordRepeat', 'on' => 'new'),				
					
		);
	}

	public function emailIsUnique($attribute, $params)
	{
		$model = UserProfile::model()->find('email=:email AND is_confirmed <> 0', array(':email' => $this->$attribute));
		if ($model)
			$this->addError ($attribute, Yii::t('app', 'The email address is already in use. Please sign in.'));
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=> Yii::t('app', 'Remember me next time'),
			'username' => Yii::t('app', 'Username'),
			'password' => Yii::t('app', 'Password'),	
			'email' => Yii::t('app', 'Email'),	
			'hasNewslatter' => Yii::t('app', 'Has newsletter')	
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())	{
			$this->_identity = new UserIdentity($this->username, $this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password',  Yii::t('app', 'Incorrect username or password.'));
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity === null)	{
			$this->_identity = new UserIdentity($this->username, $this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);
			return true;
		}	else {
			return false;
		}	
	}
}
