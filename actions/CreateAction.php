<?php
/**
 * create a new record in the database
 * 
 */
class CreateAction extends CAction
{
	public $view = 'form';
	public $form = null;
	public $modelClass = null;
	
	public function run()
	{
		if ($this->modelClass == null) {
			$controllerId = $this->controller->id;
			$this->modelClass = ucfirst($controllerId);
			$modelClass = $this->modelClass;			
		} else {
			$modelClass = $this->modelClass;
		}			
		$this->controller->model = new $modelClass('create');		
		
		
		if (isset($_POST[$modelClass])) {
			if ($this->createModel()) {
				$this->controller->redirect($this->controller->createUrl($controllerId.'/index'));				
			}
		}
		if ($this->form == null)
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		else 
			$form = $this->controller->loadForm($this->form);
		
		$this->controller->render($this->view, array(
				'model' => $this->controller->model, 
				'form' => $this->controller->formAdjust($form)));	
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