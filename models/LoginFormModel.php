<?php
/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginFormModel extends CFormModel
{
	/** 
	 * the class the create the user identity
	 * @var string
	 */
	public $identityClass = 'UserIdentityBase';
	
	public $id;
	private $_username;
	private $_email;
	private $_password;
	private $_passwordRepeat;
	private $_rememberMe=1;
	private $_hasNewsletter;
	private $_errorCode;				// code in UserIdenityBase

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
		$this->_identity = null;
		$this->_username = $value;
	}
	public function setPassword($value)
	{
		$this->_identity = null;
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
			
			/** the create scenario */	
			array('email,username, password,passwordRepeat', 'required', 'on' => 'create'),	
			array('email', 'email', 'allowEmpty'=> false, 'on' => 'create'),	
			array('has_newsletter', 'boolean', 'on'=>'create'),	
			array('password,passwordRepeat', 'length', 'min'=> 5, 'max' => 40, 'on'=>'create'),	
			array('password', 'compare', 'compareAttribute'=>'passwordRepeat', 'on' => 'create'),				
/**
 * must be moved to LoginForm
 				
			array('email', 'email', 'on' => 'login'),		
			array('email', 'emailIsUnique','on' => 'new'),				
*/					


		);
	}

	/*
	 * MUST BE MOVED TO LoginForm
	public function emailIsUnique($attribute, $params)
	{
		$model = UserProfile::model()->find('email=:email AND is_confirmed <> 0', array(':email' => $this->$attribute));
		if ($model)
			$this->addError ($attribute, Yii::t('app', 'The email address is already in use. Please sign in.'));
	}
	*/
	public function attributeLabel($key) 
	{
		$l = $this->attributeLabels();
		return isset($l[$key]) ? $l[$key] : '(not set)';
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=> Yii::t('base', 'Remember me next time'),
			'username' => Yii::t('base', 'Username'),
			'password' => Yii::t('base', 'Password'),	
			'email' => Yii::t('base', 'Email'),	
			'hasNewslatter' => Yii::t('base', 'Has newsletter')	
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())	{
			$cls = $this->identityClass;
			$this->_identity = new $cls($this->username, $this->password);
			if(! $this->_identity->authenticate()) {
				switch ($this->_identity->errorCode) {
					case UserIdentityBase::ERROR_NOT_ACTIVATED :
						$this->addError('username',  Yii::t('base', 'The account is not activated yet. Please confirm your email address by clicking on the link in the email send, or reregister.'));
						break;;
					case UserIdentityBase::ERROR_SUSPENDED :	
						$this->addError('username',  Yii::t('base', 'Your account has been suspended.'));
						break;
					case UserIdentityBase::ERROR_RANGE_BLOCK :	
						$this->addError('username',  Yii::t('base', 'You can not login from your location.'));
						break;
					default: 
						$this->addError('password',  Yii::t('base', 'Incorrect username or password.'));
				}				
			}	
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity === null)	{
			$cls = $this->identityClass;
			$this->_identity = new $cls($this->username, $this->password);
			$this->_identity->authenticate();
			$this->_errorCode = $this->_identity->errorCode;
		}

		if($this->_identity->errorCode === UserIdentityBase::ERROR_NONE) {
			$this->id = $this->_identity->id;
			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);			
			return true;
		}	else {
			$this->id = false;
			return false;
		}	
	}
	public function isEditable($fieldname)
	{
		return true;
	}
	public function isVisible($fieldname)
	{
		return true;
	}
	/**
	 * returns the error after login
	 * 
	 * @return integer const in UserIdentityBase
	 */
	public function getErrorCode()
	{
		return $this->_errorCode;
	}
	public function getErrorText()
	{
		if ($this->_errorCode > 0) {
			$cls = $this->identityClass;
			return $cls::$errorText[$this->_errorCode];
		}
		return '';
	}
}
