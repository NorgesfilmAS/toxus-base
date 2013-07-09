<?php

class AjaxCreateAction extends AjaxAction
{
	public function run($id)
	{
		$view = $this->view;
		$cd = $this->controller->definition($view, $id);
		$modelClass = $cd->childModelClass;
		$this->controller->model = new $modelClass();		
		$relationAttribute = $cd->relationAttribute;
		$this->controller->model->$relationAttribute = $id;
		
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