<?php
Yii::import('toxus.actions.BaseAction');


class PasswordRequestAction extends BaseAction
{
	public $view = 'passwordForm';
	
	public $form = 'passwordForm';
	
	public $modelClassName = 'LoginForm';
	/**
	 * default scenario to use to get the info from the form
	 * @var string
	 */
	public $scenario = 'lostpassword';
	
	/**
	 * the subject of the mail message. Is translated!
	 * @var string
	 */
	public $mailSubject = 'Request password reset';
	/**
	 * the body of the message. If false the Yii::app()->config->mail['lost_password'] is used
	 * @var string
	 */
	public $mailMessage = false;
	/**
	 * The model holding the email field
	 * @var Model
	 */
	public $userProfileModel = 'UserProfile';
	
	/**
	 * the page shown to the user after the password has been send
	 * @var string
	 */
	public $passwordSendPage = 'passwordSend';
	// $onAfterUpdate = function xx($this) : boolean
	// $params['errors'] are the errors display after the onAfterUpdate call
	
	public function run()
	{
		$view = $this->view;
		$this->controller->model = new LoginForm($this->scenario);		
		
		$form = $this->controller->loadForm($this->form);
		if (isset($_POST[$this->modelClassName])) {
			$this->controller->model->attributes = $_POST[$this->modelClassName];
			if ($this->controller->model->validate()) {
				$m = $this->userProfileModel;
				$profile = $m::model()->find('email=:email', array(':email' => $this->controller->model->email));
				if ($profile === null) {
					$this->controller->model->addError('email', Yii::t('base', 'There is no account with this email address'));
				} else {
					if ($this->onAfterUpdate !== false) {
						$result = call_user_func($this->onAfterUpdate, $profile, $this);
					} else {
						$result = call_user_func(array($this, 'sendInvitation'), $profile, $this); 
					}	
					if ($result) {
						$view = $this->passwordSendPage;
						$form = false;	// otherwise it is displayed again
					}
				}	
			}	
		}
		$params = array_merge(		
			array(
				'model' => $this->controller->model,
				'layout' => $this->pageLayout,
				'form' => $form,
				'scenario' => $this->scenario,	
				'menuItem' => $this->menuItem,
			),
			$this->params
		);
		$this->render($view, $params);		
	}			
	
	/**
	 * send the mail message to the user
	 * 
	 * @param type $model The User model requesting the new password
	 * @param action => $this
	 */
	public function sendInvitation($model, $action)
	{
		// set the mail message information
		$model->mailSubject = $this->mailSubject;
		if ($this->mailMessage) { // not translated!!!
			$model->mailMessage = $this->mailMessage;
		} else {
			$model->mailMessage = Yii::app()->config->mail['lost_password'];
		}
		
		return $model->sendMailMessage();
	}
}