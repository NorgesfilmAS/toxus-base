<?php


class Controller extends BaseController
{
	public function getVersion()
	{
		return '0.'.parent::getVersion();
	}
	/**
	 * must overload because have to create a RSRecordModel!
	 * 
	 * @param integer $key
	 * @param string $modelClass
	 * @return RSRecordModel
	 */
	public function loadModel($key, $modelClass) {
		return $modelClass::model()->findByPk($key);
	}
}