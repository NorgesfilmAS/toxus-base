<?php
/**
 * create a new record in the database
 * the scenario used is create
 * 
 * version 2.0 jvk 2013.11.06
 */
yii::import('toxus.actions.BaseAction');

class CreateAction extends BaseAction
{
	public $view = 'viewForm';
	public $form = null;
	public $modelClass = null;
	/**
	 *
	 * @var string the scenario used to get the info from the form
	 */
	public $scenario = 'create';
	
	
	public function run()
	{
		$this->checkRights();
		$controllerId = $this->controller->id;		
		if ($this->onCreateModel) {
			call_user_func($this->onCreateModel, 0, $this);
		} else {
			if ($this->modelClass == null) {
				$this->modelClass = ucfirst($controllerId);
				$modelClass = $this->modelClass;			
			} else {
				$modelClass = $this->modelClass;
			}			
			$this->controller->model = new $modelClass();		
			$this->controller->model->scenario = $this->scenario;
		}	
		
		if (isset($_POST[$modelClass])) {
			if ($this->createModel()) {
				if ($this->onAfterUpdate) {
					call_user_func($this->onAfterUpdate, $this->model->id, $this);
				}
				if ($this->successUrl) {
					$this->controller->redirect($this->controller->createUrl($this->successUrl, array('id' => $this->controller->model->id)));					
				} else {
					$this->controller->redirect($this->controller->createUrl($controllerId.'/index'));				
				}	
			}
		}
		if ($this->form == null)
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		else 
			$form = $this->controller->loadForm($this->form);
		
		$this->controller->render($this->view, array(
				'model' => $this->controller->model, 
				'form' => $form,
				'mode' => 'edit',
		));	
		
	}
	
	protected function createModel()
	{
		if ($this->controller->executeCreate()) {
			return true; // never called
		}	else {
			return false;
		}	
	}
}