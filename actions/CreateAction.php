<?php

class CreateAction extends CAction
{
	public function run()
	{
		$controllerId = $this->controller->id;
		$modelClass = ucfirst($controllerId);
		$this->controller->model = new $modelClass();
		if (isset($_POST[$modelClass])) {
			if ($this->controller->executeCreate()) {
			  $this->controller->redirect($this->controller->createUrl($controllerId.'/index'));
			}
		}
		$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		$this->controller->render('form', array(
				'model' => $this->controller->model, 
				'form' => $this->controller->formAdjust($form)));		
		
	}
}