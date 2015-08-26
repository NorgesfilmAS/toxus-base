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
	public $view = 'error';
	
	public function run()
	{
		if (($error=Yii::app()->errorHandler->error) == true) {
			if(Yii::app()->request->isAjaxRequest) // won't work
				echo $error['message'];
			else {
				$params = $error;
				$params['layout'] = 'full';
				$params['page'] = 1;							// it's the full page
				$this->controller->render($this->view, $params);
			}
		}
	}
}