<?php


class LoginAction extends CAction
{
	/**
	 * the url to open after the user did login and there is no page to open
	 * 
	 * @var string
	 */
	public $defaultUrl = 'site/home';
	/**
	 * the url that processes the login
	 * @var string
	 */
	public $loginUrl = null;
	/**
	 * the view to open for the form to login
	 */
	public $view = 'login';
	
	public function run()
	{
		$this->controller->model = new LoginForm('login');
		
		$form = $this->controller->loadForm('loginForm');
		if ($this->loginUrl)
			$form['action'] = $this->loginUrl;
		if (isset($_POST['LoginForm'])) {
			
			$this->controller->model->attributes = $_POST['LoginForm'];
			if ($this->controller->model->validate()) {
				if ($this->controller->model->login())
					$this->controller->redirect(Yii::app()->user->returnUrl != '' ? Yii::app()->user->returnUrl : $this->createUrl($this->defaultUrl));
			}	
		}
		$this->controller->render($this->view, array('form' => $form));
	}
}