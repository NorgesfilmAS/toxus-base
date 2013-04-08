<?php

Yii::import('application.vendors.toxus.models._base.BaseLogging');

class Logging extends BaseLogging
{
	private $_write = true;  // if false the record is not written
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function beforeSave() {
		if ($this->_write == false)
			return false; // just 'say' we wrote the page but we didn't ;-)
		if ($this->isNewRecord) {
			$this->url = Yii::app()->request->getRequestUri();
			$this->referer = Yii::app()->request->getUrlReferrer();
			$this->ip = Yii::app()->request->userHostAddress;
			$this->creation_date = new CDbExpression('NOW()');
		}
		return parent::beforeSave();
	}
	
	public function getWrite()
	{
		return $this->_write;
	}
	public function setWrite($value)
	{
		$this->_write = $value;
	}
}