<?php

class BaseSiteController extends Controller
{
	/**
	 * the class used to log exceptions	 
	 */
	protected $modelClass = 'DebugInfoModel';
	
	public function actions()
  {
		return array(
			'error'	  => 'toxus.actions.ErrorAction',
			'login'   => array(
										'class' => 'toxus.actions.LoginAction',
										'loginUrl' => $this->createUrl('site/login'),
					),	
			'logout'	=> 'toxus.actions.LogoutAction',
			'passwordRequest' => 'toxus.actions.PasswordRequestAction',	
			'index2' => array(
							'class' => 'toxus.actions.ViewAction',	
							'view' => 'index',
							'defaultMode' => 'hidden',
					),
			'removeAssets' => 'toxus.actions.RemoveAssetsAction',
			'systemInfo' => array(
					'class'=> 'toxus.actions.SystemInfoAction',
					'onExtraInfo' => array($this, 'systemInfo'),
			),
			'system' => array(
					'class' => 'toxus.actions.ViewAction',	
					'view' => 'system',
					'form' => 'systemSetup',
			),	
			'index' => array(
					'class' => 'toxus.actions.ViewAction',	
					'modelClass' => false,
					'view' => 'index',
			),
			'maintenance' => array(
					'class' => 'toxus.actions.ViewAction',	
					'view' => 'maintenance',
			),
      'invitation' => array(
        'class' => 'toxus.actions.InvitationAction',
				'loginModel' => 'LoginForm',	        
      ),
		);
	}	

	public function actionClearCache($id = null)
	{
		Yii::app()->imageCache->clear($id);
		$assets = new CFileHelper();

		foreach ( new DirectoryIterator (YiiBase::getPathOfAlias('webroot.assets')) as $file ) {
			if ($file->isDir() === TRUE && !($file->getBasename () === '.' || $file->getBasename () === '..')) {
				$assets->removeDirectory(YiiBase::getPathOfAlias('webroot.assets.'.$file->getBasename()));				
			}	
    }
		
		Yii::app()->user->setFlash('success', Yii::t('base', 'Assets cache and the Image cache have been cleared.'));
		$this->redirect($this->createUrl('site/systemInfo'));
	}

	public function actionDecode($filename)
	{
		$name = YiiBase::getPathOfAlias('webroot').'/'.$filename;
		if (Util::decodeUtf8File($name)) {
			echo 'Information is decoded';
		} else {
			echo 'Error: Information NOT decoded. Usage site/decode?filename=name where name is '.$name;
		}	
		Yii::app()->end();
	}
	public function actionEncode($filename)
	{
		$name = YiiBase::getPathOfAlias('webroot').'/'.$filename;
		if (Util::encodeUtf8File($name)) {
			echo 'Information is encoded';
		} else {
			echo 'Error: Information NOT decoded. Usage site/encode?filename=name where name is '.$name;
		}	
		Yii::app()->end();
	}
	
	/**
	 * stores the client error log into the debug_log table
	 */
	public function actionLog() 
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		
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
  
  /**
   * Add extra information to the systemInfo display
   * The returned format is:
   *  $properties[$key] = array(
   *  	'caption' => 'This is the header',
	 *		'items' => array(
   *      'label' => $value,
   *      'label' => array(
   *        'value' => $value,
   *        'explain' => 'The help text' 
   *      ),
   *    )
   * );
   * 
   * @param type $properties
   * @returns array
   */
  public function systemInfo($properties)
  {
    return $properties;
  }
}