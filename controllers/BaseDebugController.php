<?php
/**
 * debug controller to log / store error on the client
 * version 0.01 jvk 2014.05.17
 */

Yii::import('toxus.models.DebugInfoModel');


class BaseDebugController extends Controller
{
	/**
	 * Allow changes from other website. 
	 * @var string
	 */
	public $allowOtherOrigin = true;
	
	/**
	 * The class to use to write the debug information
	 * 
	 */

	
	public function init() {
		if ($this->allowOtherOrigin) {
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 1000');
			header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}	
		parent::init();
	}
	/**
	 * stores the client log into the debug_log table
	 */
	public function actionLog() 
	{
	//	$path = YiiBase::getPathOfAlias('application.runtime').'/debug.'.time().'.log';
		$fields = CJSON::decode(file_get_contents('php://input'));
		$modelClass = $this->modelClass;
		$model = new $modelClass();
		$model->error_url = isset($fields['errorUrl']) ? $fields['errorUrl'] : '(none)';
		$model->app_ident = isset($fields['appId']) ? $fields['appId'] : null;
		$model->device_ident = isset($fields['deviceId']) ? $fields['deviceId'] : null;
		$model->session_ident = isset($fields['sessionId']) ? $fields['sessionId'] : null;
		$model->type_id = isset($fields['typeId']) ? $fields['typeId'] : 0;
		$model->error_message = isset($fields['errorMessage']) ? $fields['errorMessage'] : null;
		$model->cause = isset($fields['cause']) ? $fields['cause'] : null;
		$model->user_id = isset($fields['userId']) ? $fields['userId'] : 0;
		$stack =  isset($fields['stackTrace']) ? $fields['stackTrace'] : array();
		$model->stacktrace = CJSON::encode($stack);
		$state = isset($fields['state']) ? $fields['state'] : array();
		$model->state = CJSON::encode($state);
		if ($model->save()) {
			echo 'ok';
		}	else {
			throw new CException('Could not save trace information: '.CHtml::errorSummary($model));
		}
	}
}