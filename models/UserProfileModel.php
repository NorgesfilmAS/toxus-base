<?php

Yii::import('application.vendors.toxus.models._base.BaseUserProfile');

class UserProfileModel extends BaseUserProfile
{
	const GUEST = 1;
	const NEWSLETTER_ONLY = 2;
	const REGISTERED_USER = 10;
	const PAYING_CUSTOMER = 100;
	const MODERATOR = 500;
	const ADMINISTRATOR = 1000;
	const GOD = 10000;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function rules() {
		return array_merge(
			parent::rules(),			
			array(
				array('username, password, email', 'required', 'on' => 'admin'),
				array('is_confirmed,is_suspended,rights_id, has_newsletter', 'numerical', 'integerOnly'=>true, 'on' => 'admin'),
				array('email', 'required', 'on' => 'newsletter'),	
			)
		);
	}

	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->creation_date = new CDbExpression('NOW()');
			$this->newsletter_key = Util::generateRandomString(30);
		}
		$md5 = md5($this->password);
		if ($this->password != $md5 || $this->login_key == '') {
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

	public function confirmed()
	{
		$this->is_confirmed = 1;
		if ($this->rights_id < UserProfile::REGISTERED_USER) {
			$this->rights_id = UserProfile::REGISTERED_USER;
		}
		$this->email = $this->email_to_confirm;
	}
	
	public function getCanEdit()
	{
		return $this->rights_id >- self::MODERATOR;
	}
}