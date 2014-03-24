<?php

/**
 * handles the basis of the login as a json call
 * 
 * there are two attributes:
 *   username
 *   password
 * 
 * 
 */
Yii::import('toxus.actions.JsonAction');

class JsonLoginAction extends JsonAction
{
	/**
	 * function to call after the login succeeds
	 * function afterLogin($id) 
	 * where id is the id of user
	 * @var string / array
	 */
	public $afterLogin = false;
	/**
	 * the class to use for the login
	 * @var string
	 */
	public $loginClass = 'LoginForm';

	// overloaded parameters
	public $scenario = 'login';
		
	public function run()
	{
		$class = $this->loginClass;
		$this->controller->model = new $class($this->scenario);
		if (isset($_POST['username'])) {
			$this->controller->model->attributes = $_POST;
			if ($this->controller->model->validate()) {
				if ($this->controller->model->login()) {
					if ($this->afterLogin) {
						if (is_array($this->afterLogin)) {
							call_user_func($this->afterLogin);
						} else {
							$func = $this->afterLogin;
							$this->controller->$func($this->controller->model->id);
						}
					}
					$this->controller->asJson();
					return;
				}
			} else {
				foreach ($this->controller->model->errors as $key => $error) {
					$this->controller->addError($key, $error);
				}
			}
			$this->controller->message = Yii::t('app', 'Combination of User, Password is wrong.');
		}	 else {
			$this->controller->message = Yii::t('app', 'No login credentials found');
			$this->controller->addError('username', Yii::t('app', 'Login credentials are wrong.'));
		}
		$this->controller->success = false;
		$this->controller->asJson();
	}
}