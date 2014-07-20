<?php
/**
 * user has a link to an invitation/password reset and clicks on it.
 * Will show the form to enter the password twice and will update the
 * password for the user.
 * On success the user is logged in and the activation key is destroyed
 * 
 * version 2.0
 * 
 */

Yii::import('toxus.actions.BaseAction');
class InvitationAction extends BaseAction
{
	/**
	 * The model holding the email field
	 * model should support functions:  
	 *		- findByInvitation(key) 
	 *				look for key and return a ActiveRecord
	 *		- createLogin()
	 *        creates the login information in the ActiveRecord
	 * the used scenarion is the 'invitation'
	 * 
	 * @var Model
	 */
	public $userProfileModel = 'UserProfile';
	
  public $pageLayout = 'full';
	/**
	 * the class to use to login the user
	 * 
	 * @var string
	 */
	public $loginModel = 'LoginFormModel';
	
	public $form = 'inviteFields';
	public $view = 'invite';
	
	public function run($k)
	{
		$params = array(
			'scenario' => $this->scenario,	
			'menuItem' => $this->menuItem,
			'form' => $this->controller->loadForm($this->form)	
		);
		$modelName = $this->userProfileModel;
		$this->controller->model = $modelName::model()->findByInvitation($k);
		
		if ($k != '' && $this->controller->model) {	// found the user, now ask for a new password
			$this->controller->model->scenario = 'invitation';
			if (isset($_POST[$modelName])) {
				$this->controller->model->attributes = $_POST[$modelName];
				if ($this->controller->model->validate()) {
					if ($this->controller->model->createLogin()) {
						$loginModel = $this->loginModel;
						$login = new $loginModel('login');
						$login->username = $this->controller->model->username;
						$login->password = $this->controller->model->passwordText;
						$login->rememberMe = 1;
						$login->login();
						$this->controller->redirect($this->controller->createUrl('site/index'));
					}
				}
			}
			$this->controller->model->passwordText = '';
			$this->controller->model->password = '';
	//		$this->render($this->view, $params);
		} else {
			$params['error'] = Yii::t('app', 'The invitation could not be found. Please contact us if you think this is an error.');
		}		
		$this->render($this->view, array_merge(	
			$params,			
			array(
				'model' => $this->controller->model,
				'layout' => $this->pageLayout,
			),
			$this->params
		));
	}
}