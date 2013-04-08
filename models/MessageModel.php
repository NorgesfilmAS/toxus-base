<?php

Yii::import('application.vendors.toxus.models._base.BaseMessage');

class MessageModel extends BaseMessage
{
	public static function model($className='Message') { //__CLASS__) {
		return parent::model($className);
	}

	public function __isset($name) {
	  if (! parent::__isset($name))		
			return isset($this->getMetaData()->columns[$name]);
		return true;
	}
	
	public function relations() {
		return array_merge(
			parent::relations(),
			array(
				'previous' => array(self::BELONGS_TO, 'Message', 'reply_to_message_id'),
				'awnser' => array(self::HAS_ONE, 'Message', 'reply_to_message_id'),	
			)
		);
	}	
	
	
	public function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->creation_date = new CDbExpression('NOW()');
		}
		$this->modified_date = new CDbExpression('NOW()');
		return parent::beforeSave();
	}
	
	public function search() {
		$this->reply_to_message_id = "0";
		$adp = parent::search();
		$adp->setSort(array('defaultOrder' => 'modified_date DESC', 'attributes' => 'modified_date DESC'));
		return $adp;
	}	
	
}