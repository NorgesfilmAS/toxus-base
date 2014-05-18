<?php

Yii::import('toxus.models._base.BaseDebugInfo');

class DebugInfoModel extends BaseDebugInfo
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