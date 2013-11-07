<?php
/**
 * create a new profile 
 * 
 */
Yii::import('application.vendors.toxus.actions.CreateAction');
class NewProfileAction extends CreateAction
{
	public $modelClass = 'UserProfile';
	public $view = 'formDialog';
	public $form = 'newProfile';
	/**
	 * the action to call to create a new profile
	 * @var string
	 */	
	public $action = 'site/newProfile';
	/**
	 *
	 * @var string the view to show when the profile has been created and should be confirmed
	 */
	public $viewDone = 'newProfileDone';
	/**
	 * 
	 * @return string the mail template to use to send the confirmation / check
	 * should be in the mail directory
	 */
	public $confirmMail = 'confirmEmail';
	
	public $autoLoginOnConfirm = true;
	
	public function run()
	{
		if (isset($_GET['confirm']) && $_GET['confirm'] == 1 && isset($_GET['key'])) {
			// user is trying to confirm a link
			$modelClass = $this->modelClass;
			$profile = $modelClass::model()->find('login_key=:guid', array(':guid' => $_GET['key']));
			if ($profile) {
				$confirm = $profile->confirmed();
				$profile->save();
				if ($this->autoLoginOnConfirm) {
					$login = new LoginFormModel();
					$login->username = $profile->username;
					$login->password = $profile->password;
					$login->login();
				}
				$this->controller->render('formDialog', array(
					'title' => $this->controller->te('profile confirmed'),
					'message' => $this->controller->te('your profile has been activated.<br/><br />').$this->controller->te('have fun on the site!<br />').
						($this->autoLoginOnConfirm ? ' ' :'<a class="btn btn-info" href="'.$this->controller->createUrl('site/login').'">'.$this->controller->te('Login').'</a>')
				));					
			} else {
				$this->controller->render('formDialog', array(
					'title' => $this->controller->te('error creating profile'),
					'errorText' => $this->controller->te('the account could not be actived. The unique key was not found.<br/><br/>Please try to register again.'),	
					'message' => '<a class="btn btn-info" href="'.$this->controller->createUrl($this->action).'">'.$this->controller->te('Register').'</a>'		
				));
			}
		} else {
			parent::run();
		}
	}
	/**
	 * create a new profile and send a mail to the user
	 * 
	 * @return boolean false if profile was not created
	 */
	protected function createModel()
	{
		$transaction = Yii::app()->db->beginTransaction();
		try {
			// must use transaction because if mail goes wrong the information should not be written
			// check that the user is not trying to restart a password session
			$model = $this->modelClass;
			$this->controller->model->attributes = $_POST[$model];
			$unfinishedProfile = $model::model()->find('email_to_confirm=:email and is_confirmed=0', array(':email' => $this->controller->model->email));
			if ($unfinishedProfile) {
				// move the new information to the found model so it becomes an update
				$unfinishedProfile->setScenario('create');
				$unfinishedProfile->attributes = $_POST[$model];
				$this->controller->model = $unfinishedProfile;
			}
			
			if ($this->controller->executeCreate()) {
				// send the message to the user to confirm the newly created profile.
				if (!$this->controller->model->sendConfirmation()) {
					$transaction->rollback();
					return false;
				};
				/**
				 * tell the user all went well
				 */
				$this->controller->render($this->viewDone, array('model' => $this->controller->model));
				$transaction->commit();
				Yii::app()->end(200);
			} else {
				$transaction->rollback();
				return false;
			}
		} catch (Exception $e) {
			$this->controller->model->addError('email', $e->getMessage());
			$transaction->rollback();
		}	
	}
}