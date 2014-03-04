<?php

Yii::import('toxus.actions.JsonAction');

class JsonLogoutAction extends JsonAction
{
	public function run()
	{
		Yii::app()->user->logout();
		$this->controller->asJson();
	}
}
