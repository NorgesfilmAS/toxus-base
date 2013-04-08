<?php

Yii::import('application.vendors.toxus.models._base.BaseMail');

class MailModel extends BaseMail
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->creation_date = new CDbExpression('NOW()');
		}
		return parent::beforeSave();
	}
}