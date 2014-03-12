<?php

class BaseSetupController extends Controller
{
	public $setupClass = 'SetupFormModel';
	
	public function actionIndex($id=null, $mode='edit')
	{
		if (!(Yii::app()->user->getState('adminId', null) != null)) {
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
		if (isset($_POST['Login'])) {
			$params['password'] = isset($_POST['Login']['password']) ? $_POST['Login']['password'] : '';
			if ($params['password'] <> '') {
				$s = Yii::app()->config->security['password'];
				if ($params['password'] == $s) {
					Yii::app()->user->setState('adminId', 1);
					$this->redirect($this->createUrl('setup/index'));
				} else {
					$params['errorText'] = Yii::t('config', 'Invalid password');
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
}
