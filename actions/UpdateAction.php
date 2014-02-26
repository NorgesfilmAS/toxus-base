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
		$this->controller->model = $modelClass::model()->findByPk($id);
		$this->controller->model->scenario = $this->scenario;
		
		$mode =  isset($_GET['mode']) ? $_GET['mode'] : 'view';
						
		if (isset($_POST[$modelClass])) {
			if ($this->controller->executeUpdate()) {
				/* this was in PNEK 
				if ($this->view == null) {
					$this->view = Yii::app()->baseURL.'/'.Yii::app()->request->pathInfo;
				}
				$this->controller->redirect($this->view);
				//$this->controller->redirect($this->controller->createUrl($controllerId.'/'.$this->view, array('id' => $id)));
			}
				 * 
				 */
				if ($this->successUrl) {
					$this->controller->redirect($this->controller->createUrl($this->successUrl, array('id' => $id)));					
				}
				if (false && $this->successUrlFull != null) {
					$this->controller->redirect($this->successUrlFull);
				}
				$mode = 'view';							
			}
		}
		if ($this->form == null)
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		else 
			$form = $this->controller->loadForm($this->form);
		
		$this->render( $this->view, array(
				'model' => $this->controller->model,
				'layout' => 'ajaxForm',		// WHY???
				'layout' => $this->pageLayout,
				'form' => $form,	
				'mode' => $mode,
				'state' => $mode,
				'menuItem' => $this->menuItem,
				'transactionId' => isset($_GET['transaction']) ? $_GET['transaction'] : 0,
		));
		
	}
}