<?php

class AjaxDeleteAction extends AjaxAction
{
	
	public function run($id)
	{
		// not tested and NOT YET WORKING!!!!!
		
		$view = $this->view;
		$cd = $this->controller->definition($view, null, $id);	
		$this->controller->model = $cd->childModel;
//		$modelClass = $cd->childModelClass;		
	
		if ($this->controller->executeDelete()) {
			echo 'ok';			
		} else {
			echo 'error';
		}
		return;
	}		
}