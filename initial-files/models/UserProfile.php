<?php

Yii::import('application.vendors.toxus.models.UserProfileModel');

class UserProfile extends UserProfileModel
{
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
}