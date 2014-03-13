<?php
/**
 * update a record with the menu on the left site.
 * 
 */
Yii::import('toxus.actions.BaseAction');

class UpdateAction extends BaseAction
{
	/*
	 * version 1.1: Introduced in PNEK 8/8/2013: $view and $edit defaults are changed to 
	 * viewForm so it will default load a page that can switch between edit and view
	 * 
	 */
	public $view = 'viewForm';		// the view to open to edit the current information
	public $form = null;					// the name of the form ex extension
	public $menuItem = null;			// the menu item to active. Should be a jQuery selector (#menu-agent, or .agent-item)
	public $scenario = 'update';	// default scenario to use to update the information
	
	public function run($id, $mode='view')
	{
		if (!$this->allowed) {
			throw new CHttpException(403, Yii::t('app', 'Access denied'));
		}
		$controllerId = ucfirst($this->controller->id);
		$modelClass = $this->modelName;		
		if ($this->onCreateModel) {
			call_user_func($this->onCreateModel, $id, $this);
		} else {
			$this->controller->model = $modelClass::model()->findByPk($id);
		}	
		$this->controller->model->scenario = $this->scenario;		
		$mode =  isset($_GET['mode']) ? $_GET['mode'] : 'view';
						
		if (isset($_POST[$modelClass])) {
			if ($this->controller->executeUpdate()) {
				if ($this->onAfterUpdate) {
					call_user_func($this->onAfterUpdate, $id, $this);
				}
				if ($this->successUrl) {
					$this->controller->redirect($this->controller->createUrl($this->successUrl, array('id' => $id)));					
				} else {
					$this->controller->redirect($this->controller->createUrl($this->controller->route, array('id' => $id)));					
				}
			}
		}
		if ($this->form == null) {
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		} elseif (is_string($this->form)) { 
			$form = $this->controller->loadForm($this->form);
		} else {
			$form = $this->form;
		}	

		$params = array_merge(		
			array(
				'model' => $this->controller->model,
	
				'layout' => $this->pageLayout,
				'form' => $form,	
				'mode' => $mode,
				'state' => $mode,
				'menuItem' => $this->menuItem,
			),
			$this->params
		);
		$this->render($this->view, $params);
/*		
		$this->render( $this->view, array(
				'model' => $this->controller->model,
	//			'layout' => 'ajaxForm',		// WHY???
				'layout' => $this->pageLayout,
				'form' => $form,	
				'mode' => $mode,
				'state' => $mode,
				'menuItem' => $this->menuItem,
//				'transactionId' => isset($_GET['transaction']) ? $_GET['transaction'] : 0,
		));
*/		
	}
}