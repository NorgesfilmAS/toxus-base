<?php

class PasswordRequestAction extends CAction
{
	public $view = 'passwordForm';
	
	public function run()
	{
		$this->controller->model = new LoginForm('password');		
		$form = $this->controller->loadForm('passwordForm');
		if (isset($_POST['LoginForm'])) {
			$this->controller->model->attributes = $_POST['LoginForm'];
			if ($this->controller->model->validate()) {
				$profile = UserProfile::model()->find('email=:email', array(':email' => $this->controller->model->email));
				if ($profile === null) {
					Yii::app()->user->setFlash('error', 'There is no record found for this email address');
				} else {
					$mail = new MailMessage();
					if ($mail->render('requestPassword', array('model' => $profile))) {
						$this->render('passwordSend', array('model' => $this->model));
						return;
					}	
				}	
			}	
		}
		$this->controller->render($this->view, array('model' => $this->controller->model, 'form' => $form));				
	}					
}