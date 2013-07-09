<?php

class AjaxEditAction extends AjaxAction
{
	/**
	 * 
	 * @param int $id the id of the item to show,not of the view.
	 */
	public function run($id)
	{
		
		$view = $this->view;
		$cd = $this->controller->definition($view, null, $id);	
		$this->controller->model = $cd->childModel;
		$modelClass = $cd->childModelClass;		
		
		if ($_POST[$modelClass]) {
			if ($this->controller->executeUpdate($id))  {
				$cd->childRelationId = $this->controller->model->id;
				echo 'ok';
				return;	
			}	
		}	
		
		$this->controller->render('ajaxForm', array(
				'model' => $cd->childModel, 
				'form' => $cd->form,
				'sub' => $cd)
		, false, true);				

	}
}