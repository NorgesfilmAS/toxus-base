<?php

Yii::import('toxus.models._base.BaseUserProfile');

class UserProfileModel extends BaseUserProfile
{
	const GUEST = 1;
	const NEWSLETTER_ONLY = 2;
	const REGISTERED_USER = 10;
	const PAYING_CUSTOMER = 100;
	const MODERATOR = 500;
	const ADMINISTRATOR = 1000;
	const GOD = 10000;
	
	private $_passwordRepeat = null; // not used but needed for the create scenario
	
	/**
	 * the url to use to confirm the creation of the account
	 *  
	 * @var string
	 */
	public $confirmationUrl = 'site/profile';
	/**
	 * the template to use to send the confirmation mail to the user
	 * 
	 * @var string
	 */
	public $confirmMail = 'confirmMail';
	
	/**
	 * the message to send to the user when requesting password / invite
	 * @var string
	 */
	public $mailSubject = false;
	public $mailMessage = false;
	
	/**
	 * the field used to auto login for invitation and password reset
	 * 
	 * @var string
	 */
	public $activationKeyField = 'login_key';
	/**
	 * the url used to login and request a new password or active the accoutn
	 * @var string
	 */
	public $autoLoginUrl = 'site/invitation';
	
	/**
	 * the class to log the mail messages. If false message not logged
	 * 
	 * @var string|bool name of the model class
	 */
	protected $mailLogModel = 'MailModel';
	
  /**
   * the class used to login the user
   * 
   * @var string
   */
  public $userIdentityClass = 'UserIdentityBase';
  
  /** the invitation */
	public $_mailMessage = false;
	public $_mailSubject = false;
	public $_passwordText = false;
	public $_passwordTextRepeat = false;
  
  
	public function getPasswordRepeat()
	{
		return $this->_passwordRepeat;
	}
	public function setPasswordRepeat($value)
	{
		$this->_passwordRepeat = $value;
	}	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function rules() {
		return array_merge(
			parent::rules(),			
			array(
				array('username, password, email', 'required'),
				array('is_confirmed,is_suspended,rights_id, has_newsletter', 'numerical', 'integerOnly'=>true, 'on' => 'admin'),
				array('email', 'required', 'on' => 'newsletter'),	
				array('username, email', 'unique'),					
					
				array('email', 'email'),	
				array('password', 'length', 'min'=>5, 'max'=>64, 'tooShort'=> Yii::t('base','Password is too short (minimum is 5 characters)')),        					
				array('password', 'compare', 'compareAttribute'=>'passwordRepeat', 'on' => 'create'),									
				array('username,email,password,passwordRepeat', 'required', 'on' => 'create'),	
					
				array('email,password,username,is_confirmed', 'safe', 'on'=>'createAdmin'),	
				
				array('email,password,username,is_confirmed,is_suspended,rights_id,has_newsletter,newsletter_key', 'safe', 'on'=>'update'),	
        
        // the invitations	
        array('passwordText,passwordTextRepeat, accepted_terms', 'required', 'on' => 'invitation'),
        array('passwordText', 'compare', 'compareAttribute'=>'passwordTextRepeat', 'on' => 'invitation'),									
        array('accepted_terms', 'compare', 'compareValue'=>1, 'on' => 'invitation', 'message' => Yii::t('base', 'You must accept the terms.')),													
        array('passwordText', 'length', 'min'=>5, 'max'=>64, 'tooShort'=> Yii::t('base','Password is too short (minimum is 5 characters)'), 'on' => 'invitation'),        													        
			)
		);
	}

	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->creation_date = new CDbExpression('NOW()');
			$this->newsletter_key = Util::generateRandomString(30);			
		}
		if ($this->isNewRecord && $this->is_confirmed == 0) {
			/** swap the email and email to confirm */
			$this->email_to_confirm = $this->email;
	//		$this->email = null;
		}
		$md5 = md5($this->password);
		if ($this->password_md5 != $md5 || $this->login_key == '') {
			$this->password_md5 = $md5;
			$this->login_key = Util::generateRandomString(30);
		}	
	  $this->modified_date = new CDbExpression('NOW()');		
		return parent::beforeSave();
	}
	
	
	static public function getRightsOptions()
	{
		return array(
			self::GUEST => Yii::t('base', 'Guest'),
			self::REGISTERED_USER => Yii::t('base', 'Registered User'),	
			self::PAYING_CUSTOMER => Yii::t('base', 'Paying Customer'),					
			self::MODERATOR => Yii::t('base', 'Moderator'),
			self::ADMINISTRATOR => Yii::t('base', 'Administrator'),					
			self::GOD => Yii::t('base', 'God'),					
		);
	}

	public function getRightsText()
	{
		$a = $this->getRightsOptions();
		return $a[$this->rights_id];
	}

	/**
	 * make the account confirmed.
	 * @return boolean true if the confirm it 'new' false if it was already confirmed
	 */
	public function confirmed()
	{
		$wasConfirmed = $this->is_confirmed;
		$this->is_confirmed = 1;
		if ($this->rights_id < UserProfile::REGISTERED_USER) {
			$this->rights_id = UserProfile::REGISTERED_USER;
		}
		$this->email = $this->email_to_confirm;
		return $wasConfirmed != 1;
	}
	/**
	 * send a confirmation mail to the user
	 * 
	 */
	public function sendConfirmation()
	{
		$mailClass = Yii::app()->config->mail['mailer'];
		$mm = new  $mailClass();
		$mm->mailLogModel = $this->mailLogModel;
		
		if (! $mm->render($this->confirmMail, array(
			'model' => $this,
			'action' => $this->confirmationUrl,		
		), true)) {
			$this->addError('email', Yii::t('base', 'Unable to send mail'));
			return false;
		}	
		return true;
	}
	
	public function getCanEdit()
	{
		return $this->rights_id >= self::MODERATOR;
	}
	
	public function isConfirmedOptions()
	{
		return array(
			'0' => Yii::t('base', 'No'),
			'1' => Yii::t('base', 'Yes'),	
		);
	}
	
	/**
	 * handeling lost password etc
	 */
/**
	 * sets the password to a unique key for invitations
	 */
	protected function generateInvitation()
	{
		$field = $this->activationKeyField;
		$this->$field = Util::generateRandomString(60);
		$this->save();
	}
	
	/**
	 * sends the user a new login help key
	 * 
	 * @return boolean
	 */
	public function sendMailPasswordRequest()
	{
		$field = $this->activationKeyField;					// the field that hold the activation definition
		$this->generateInvitation();								// generate a new key for this user
		$mailClass = Yii::app()->config->mail['mailer'];
		$mail = new $mailClass();
		$mail->mailLogModel = $this->mailLogModel;
		return $mail->render(
						'requestPassword', 
						array(
							'model' => $this,
							'keyFieldname' => $field
						), 
						false);	
	}
	
	/**
	 * NOT USED. Is Replace by the SendMailPasswordRequest for more flexibility
	 * 
	 * sends the message to the user for a invitation or for lost password
	 * message and subject can be set thrue the mail[] invite_subject and invite_message config
	 */
	public function sendMailMessage($configKey = false)
	{
		$field = $this->activationKeyField;
		$this->generateInvitation();
		$mailClass = Yii::app()->config->mail['mailer'];
		$mail = new $mailClass();
		$mail->mailLogModel = $this->mailLogModel;
		
		if ($configKey) {	// generate the message from a key in the configuration definition
			$subject = Yii::app()->config->value($configKey.'.subject');
			if (!$subject) {
				$subject = Yii::t('app', 'Request password reset');			
			}			
			$message = Yii::app()->config->value($configKey.'.message');
			if (!$message) {
				$message = Yii::app()->config->mail['invite_message'];			
			}
			$html = Yii::app()->config->value($configKey.'.html');
		} else {
			$subject = $this->mailSubject ? $this->mailSubject : Yii::app()->config->mail['invite_subject'];
			$message = $this->mailMessage ? $this->mailMessage : Yii::app()->config->mail['invite_message'];
			$html = false;
		}
		return $mail->send($this->email, $subject, $message, array(
			'{signin}' => Yii::app()->createAbsoluteUrl($this->autoLoginUrl, array('k' => $this->$field)),
			'html' => $html
		));
	}	
	
	/**
	 * Find the user by the unique key for the invitation
	 * 
	 * @param string $key the unique key
	 * @returns null or the user_profile for the key
	 */
	public function findByInvitation($key)
	{
		return $this->find('login_key=:key',array(
				':key' => $key 	
		));
	}	
	/**
	 * set the username and password for this user
	 * uses the UserIdentity to create the password
	 * 
	 * and saves the record 
	 */
	public function createLogin()
	{
		Yii::app()->user->logout();
    $ui = $this->userIdentityClass;
    $usr = new $ui($this->username, $this->passwordText);
		$this->password = $usr->makePassword();
		$this->login_key = null;
		return $this->save();			
	}
  
  public function getPasswordText()					
	{
		return $this->_passwordText;
	}
	public function setPasswordText($value)
	{
		$this->_passwordText = $value;
	}
	public function getPasswordTextRepeat()
	{
		return $this->_passwordTextRepeat;
	}
	public function setPasswordTextRepeat($value)
	{
		$this->_passwordTextRepeat = $value;
	}
	  
}