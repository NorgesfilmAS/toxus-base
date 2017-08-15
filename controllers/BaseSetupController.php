<?php

class BaseSetupController extends Controller
{
	public $setupClass = 'SetupFormModel';
	
	public function actionIndex($id=null, $mode='edit')
	{
		if (!(Yii::app()->user->getState('adminId', null) != null)) {
      Yii::log('redirecting to login', CLogger::LEVEL_INFO, 'toxus.login');
			$this->redirect($this->createUrl('setup/login'));
		}
		$class = $this->setupClass;
		$this->model = new $class();
		if (isset($_POST[$class])) {
			$this->model->attributes = $_POST[$class];
			if ($this->model->validate()) {
				$this->model->save();
			}
		}
		$params = array(
			'form' => $this->model->generateForm(),
			'model' => $this->model,	
			'layout' => 'content',
			'mode' => $mode,	
			'state' => $mode,		
		);
		
		$this->render('viewForm', $params);
	}
	
	public function actionLogin()
	{
		$params = array('form' => $this->loadForm('loginForm'));
		if (isset($_POST['SetupFormModel'])) {
			$params['password'] = isset($_POST['SetupFormModel']['password']) ? $_POST['SetupFormModel']['password'] : '';
			if ($params['password'] <> '') {
				$s = Yii::app()->config->security['password'];
				if ($params['password'] == $s) {
					Yii::app()->user->setState('adminId', 1);
					$this->redirect($this->createUrl('setup/index'));
				} else {
					$params['errorText'] = Yii::t('base', 'Invalid password');
				}
			}
		}
		$this->render('formDialog', $params);
	}
	
	public function actionLogout()
	{
		Yii::app()->user->setState('adminId', null);
		$this->redirect($this->createUrl('setup/index'));
	}
	
	public function actionInfo()
	{
		phpinfo();
	}
  
  /**
   * auto migrate for call after update of git
   * 
   * UNTESTED YET
   */
  public function actionMigrate() {
    $this->runMigrationTool();
  }
  /**
   * http://www.yiiframework.com/wiki/226/run-yiic-directly-from-your-app-without-a-shell/
   */
  private function runMigrationTool() {
    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
    $runner = new CConsoleCommandRunner();
    $runner->addCommands($commandPath);
    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
    $runner->addCommands($commandPath);
    $args = array('yiic', 'migrate', '--interactive=0');
    ob_start();
    $runner->run($args);
    return htmlentities(ob_get_clean(), null, Yii::app()->charset);
  }
}
