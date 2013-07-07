<?php

class AjaxCreateAction extends AjaxAction
{
	public function run($id)
	{
		if ($this->controller->executeCreate($id)) return;	// will end if ok
		
		$view = $this->view;
		$cd = $this->controller->subFrameDefinition($view, $id);
		
		$form = $this->controller->loadForm(lcfirst($cd->modelClass). 'Fields');
		$s = $cd->modelClass;
		$this->controller->model = new $s();
		$this->controller->renderAjax('ajaxForm', array(
				'model' => $this->controller->model, 
				'form' => $form, 
				'sub' => $cd));		
	}
		
}