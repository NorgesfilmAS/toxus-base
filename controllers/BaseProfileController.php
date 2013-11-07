<?php

class BaseProfileController extends Controller
{
	public $modelClass = 'UserProfile';
	
	public function actions()
	{
		return array(
				'list' => array(
					'class' => 'toxus.actions.ListAction',
					'allowed' => Yii::app()->user->isAdmin,
					'modelClass' => $this->modelClass,	
				),		
				'create' => array(
					'class' => 'toxus.actions.CreateAction',
					'modelClass' => $this->modelClass,	
				  'allowed' => Yii::app()->user->isAdmin,						
					'view' => 'formDialog',
					'scenario' => 'adminCreate',	
				),
				'delete' => array(
					'class' => 'toxus.actions.DeleteAction',
					'modelClass' => $this->modelClass,
				  'allowed' => Yii::app()->user->isAdmin,			
					'successUrl' => $this->createUrl('profile/list'),	
				)
		);				
	}
	
	public function actionIndex()
	{
		if (Yii::app()->user->isGuest) {
			$this->redirect($this->createUrl('profile/login'));
		} else {
			$this->render('index', array('model' => Yii::app()->user->profile));
		}	
	}
	
	/**
	 * updates of user profile basics
	 */
	public function actionUpdate()
	{
		Yii::import('application.controllers.ArticleController');
		
		$article = new ArticleController('Article');		
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
						$article->actionIndex('email-confirm');
						return;
					}	
				  $this->redirect($this->createUrl('profile/index'));
				}
			}	
		}
		$this->model->email_to_confirm = $this->model->email;
		$this->render('update', array('model' => $this->model, 'form' => $form));
	}
	
}