<?php
/**
 * report the error to the user.
 * 
 * version 2.0 nov 2013 JvK
 */

Yii::import('toxus.actions.BaseAction');
class ErrorAction extends BaseAction
{
	/**
	 * overloaded because need a new error page definition
	 * 
	 * @var string
	 */
	public $view = 'errorPage';
	
	public function run()
	{
		if (($error=Yii::app()->errorHandler->error) == true) {
			if(Yii::app()->request->isAjaxRequest) // won't work
				echo $error['message'];
			else
				$this->controller->render($this->view, $error);
		}
	}
}