<?php

class BaseProfileController extends Controller
{
	
	/**
	 * the name of the form to display
	 * @var string
	 */
	public $profileForm = 'profileForm';
	
	private $_modelClass = false;
	
	protected $adminActions = array('list','create','delete', 'update');
	
	public function init() {
		$this->_modelClass = get_class(Yii::app()->user->profile);
		parent::init();
	}
	
	public function actions()
	{
		return array(
				'list' => array(
					'class' => 'toxus.actions.ListAction',
					'allowed' => Yii::app()->user->isAdmin,
					'modelClass' => $this->_modelClass,	
				),		
				'create' => array(
					'class' => 'toxus.actions.CreateAction',
					'modelClass' => $this->_modelClass,	
				  'allowed' => Yii::app()->user->isAdmin,						
					'view' => 'formDialog',
					'scenario' => 'adminCreate',
					'form' => 'createAdminFields'	
				),
				'delete' => array(
					'class' => 'toxus.actions.DeleteAction',
					'modelClass' => $this->_modelClass,
				  'allowed' => Yii::app()->user->isAdmin,			
					'successUrl' => $this->createUrl('profile/list'),	
				),
				'view' => array(
					'class' => 'toxus.actions.ViewAction',
					'modelClass' => $this->_modelClass,	
					'form' => 'overviewFields',	
					'allowed' => Yii::app()->user->isAdmin,
					'menuItem' => '.menu-overview',	
				),
				'update' => array(
					'class' => 'toxus.actions.UpdateAction',
					'modelClass' => $this->_modelClass,
					'form' => 'propertiesFields',		
				  'allowed' => Yii::app()->user->isAdmin,		
					'menuItem' => '.menu-properties',	
				),
				'index' => array(
					'class' => 'toxus.actions.UpdateAction',
					'allowed' => ! Yii::app()->user->isGuest,
					'form' => $this->profileForm,
					'modelClass' => $this->_modelClass,
					'pageLayout' => 'full',	
					'onCreateModel' => array($this, 'openProfile'),
					'view' => 'index',	
				)
				
		);				
	}
	
	public function openProfile()
	{
		$this->model = Yii::app()->user->profile;
	}
	
}