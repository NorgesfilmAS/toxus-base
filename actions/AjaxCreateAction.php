<?php

class AjaxCreateAction extends AjaxAction
{
	public $form = null;
	
	public function run($id=null)
	{
		$view = $this->view;
		$cd = $this->controller->definition($view, $id);
		
		$modelClass = $cd->childModelClass;
		$this->controller->model = new $modelClass();		
		
		$this->controller->model->{$cd->relationAttribute} = $id;
		
		if ($_POST[$modelClass]) {
			if ($this->controller->executeCreate($id))  {
				echo 'ok';
				return;	
			}	
		}	

		$this->controller->renderAjax('ajaxForm', array(
				'model' => $cd->childModel, 
				'form' => $cd->form, 
				'sub' => $cd));		
	}
		
}