<?php

class BaseProfileController extends Controller
{
	public function actionIndex()
	{
		if (Yii::app()->user->isGuest) {
			$this->redirect($this->createUrl('profile/login'));
		} else {
			$this->render('index', array('model' => Yii::app()->user->profile));
		}	
	}
	
	/**
	 * Login in the user
	 */
	public function actionLogin()
	{
		$this->model = new LoginForm('login');
		$form = $this->loadForm('loginForm');
		if (isset($_POST['LoginForm'])) {
			$this->model->attributes = $_POST['LoginForm'];
			if ($this->model->validate()) {
				if ($this->model->login())
					$this->redirect(Yii::app()->user->returnUrl != '' ? Yii::app()->user->returnUrl : $this->createUrl('profile/index'));
			}	
		}
		$this->render('login', array('model' => $this->model, 'form' => $form));
	}
	/**
	 * logoff the user
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);		
	}
	
	/**
	 * resends the password to the user
	 * 
	 */
	public function actionPassword()
	{
		$this->model = new LoginForm('password');		
		$form = $this->loadForm('passwordForm');
		if (isset($_POST['LoginForm'])) {
			$this->model->attributes = $_POST['LoginForm'];
			if ($this->model->validate()) {
				$profile = UserProfile::model()->find('email=:email', array(':email' => $this->model->email));
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
		$this->render('passwordForm', array('model' => $this->model, 'form' => $form));		
	}
	
	/**
	 * Create an new, not confirmed profile
	 * 
	 * @return type
	 */
	public function actionNew()
	{
		Yii::import('application.controllers.ArticleController');
		
		$this->model = new LoginForm('new');
//		$form = $this->loadForm('newRegistration');
		$article = new ArticleController('Article');		
		if (isset($_POST['LoginForm'])) {
			$this->model->attributes = $_POST['LoginForm'];
			if ($this->model->validate()) {
				// generate a new temp profile and send the email
				$model = UserProfile::model()->find('email=:email', array(':email' => $this->model->email));
				if ($model == null)
					$model = new UserProfile();
				$model->username = $this->model->username;
				$model->password = $this->model->password;
				$model->email = $this->model->email;
				$model->email_to_confirm = $this->model->email;
				if ($model->validate()) {
					if ($model->save()) {
						$mail = new MailMessage();
						$mail->render('confirmEmail', array('model' => $model));
						$article->actionIndex('registration-started');
						//$this->render('confirmationSend', array('model' => $this->model, 'profile' => $model));
						return;
					}	
				}	
			}
		}
		// ----
		// this should render: article/registration: which is article/index?id=[n]
		// so how to move the $model to that controller?
		//
		//$this->render('newRegistration', array('model' => $this->model, 'form'=> $form));
		$article->actionIndex('register');
	}
	
	/**
	 * confirms the profile by validation the key against the login_key in the profile.
	 * Throws error if not valid
	 * 
	 * @param string $key
	 * @throws CHttpException
	 */
	public function actionConfirm($key)
	{
		$model = UserProfile::model()->find('login_key=:slug', array(':slug' => $key));
		if ($model == null)
			throw new CHttpException(500, 'Profile not found');
		// we should login with this profile....
		$ident = new UserIdentity($model->username, $model->password);
	  $ident->authenticate();
		if (!($ident->errorCode == UserIdentity::ERROR_NONE || $ident->errorCode == UserIdentity::ERROR_NOT_ACTIVATED)) {
			throw new CHttpException(500, 'Profile not valid');
		}
		$model->confirmed();
		if (!$model->save()) {
			throw new CHttpException(500,'The information could not be saved. Please try later again: '. Yii::app()->params['debug'] ? var_export($model->errors, true) :'');
		}	
		if ($ident->errorCode == UserIdentity::ERROR_NOT_ACTIVATED)
			$ident->authenticate();
		Yii::app()->user->login($ident);
		$this->redirect($this->createUrl('profile/index'));
	}
	
	/**
	 * updates of user profile basics
	 */
	public function actionUpdate()
	{
		$this->model = Yii::app()->user->profile;
		$form = $this->loadForm('updateForm');
		if (isset($_POST['UserProfile'])) {
			// check for a changed email address
			$mustConfirm = $this->model->email !== $_POST['UserProfile']['email'];
			$this->model->attributes = $_POST['UserProfile'];
			if ($this->model->validate()) {
				if ($mustConfirm) {
					$temp = $this->model->email_to_confirm;
					$this->model->email_to_confirm = $this->model->email;
					$this->model->email = $temp;
					$this->model->login_key = '';
				}	
				if ($this->model->save()) {			  
					if ($mustConfirm) {
						$mail = new MailMessage();
						$mail->render('emailAddressChanged', array('model' => $this->model));						
						$this->render('emailAddressChanged', array('model', $this->model));
						return;
					}	
				  $this->redirect($this->createUrl('profile/index'));
				}
			}	
		}
		$this->model->email_to_confirm = $this->model->email;
		$this->render('update', array('model' => $this->model, 'form' => $form));
	}
	
	/**
	 * 
	 * @param string $id
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->user->isAdmin === false)
			$this->redirect($this->createUrl('profile/login'));
		$this->model = UserProfileModel::model()->find('id=:id', array(':id' => $id));
		if ($this->model == null) throw new CDbException('profile not found');
		if ($this->model->delete() == false)
			if ($this->model == null) throw new CDbException('Profile can not be deleted.');
		$this->redirect($this->createUrl('profile/list'));	
	}
		  
	public function actionList()
	{
		if (! Yii::app()->user->isAdmin) $this->redirect(Yii::app()->homeUrl);
		
		$model = new UserProfileModel();
		$model->unsetAttributes();
		if ($_REQUEST['UserProfile']) {
			$model->attributes = $_REQUEST['UserProfile'];
		}
		$this->render('list', array('model' => $model));
	}

}