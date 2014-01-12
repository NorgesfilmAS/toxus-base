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
				array('password', 'length', 'min'=>5, 'max'=>64, 'tooShort'=> Yii::t('app','Password is too short (minimum is 5 characters)')),        					
				array('password', 'compare', 'compareAttribute'=>'passwordRepeat', 'on' => 'create'),									
				array('username,email,password,passwordRepeat', 'required', 'on' => 'create'),	
					
				array('email,password,username,is_confirmed', 'safe', 'on'=>'createAdmin'),	
				
				array('email,password,username,is_confirmed,is_suspended,rights_id,has_newsletter,newsletter_key', 'safe', 'on'=>'update'),	
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
			$this->email = null;
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
			self::GUEST => Yii::t('app', 'Guest'),
			self::REGISTERED_USER => Yii::t('app', 'Registered User'),	
			self::PAYING_CUSTOMER => Yii::t('app', 'Paying Customer'),					
			self::MODERATOR => Yii::t('app', 'Moderator'),
			self::ADMINISTRATOR => Yii::t('app', 'Administrator'),					
			self::GOD => Yii::t('app', 'God'),					
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
		$mm = new MailMessage;
		if (! $mm->render($this->confirmMail, array(
			'model' => $this,
			'action' => $this->confirmationUrl,		
		))) {
			$this->addError('email', Yii::t('app', 'Unable to send mail'));
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
			'0' => Yii::t('app', 'No'),
			'1' => Yii::t('app', 'Yes'),	
		);
	}
	
}