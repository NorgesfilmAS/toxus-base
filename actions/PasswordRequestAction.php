<?php

class PasswordRequestAction extends CAction
{
	public $view = 'passwordForm';
	
	/**
	 * if true the function UserProfile->resetPassword() is called before sending the mail
	 * 
	 * @var boolean
	 */
	public $resetPassword = false;
	
	public function run()
	{
		$this->controller->model = new LoginForm('password');		
		$form = $this->controller->loadForm('passwordForm');
		if (isset($_POST['LoginForm'])) {
			$this->controller->model->attributes = $_POST['LoginForm'];
			if ($this->controller->model->validate()) {
				$profile = UserProfile::model()->find('email=:email', array(':email' => $this->controller->model->email));
				if ($profile === null) {
					$this->controller->model->addError('email', 'There is no account with this email address');
				} else {
					if ($this->resetPassword) {
						if (!$profile->resetPassword()) {
							$this->controller->model->addError('email', 'The password could not be reset');
						}
					}
					if (!$this->controller->model->hasErrors()) {
						$mail = new MailMessage();
						if ($mail->render('requestPassword', array('model' => $profile))) {
							$this->controller->render('passwordSend', array('model' => $this->controller->model));
							return;
						}	
					}	
				}	
			}	
		}
		$this->controller->render($this->view, array('model' => $this->controller->model, 'form' => $form));				
	}					
}