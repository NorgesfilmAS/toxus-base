<?php

class AjaxIndexAction extends AjaxAction
{
	
	public function run($id, $view=null)
	{
		if ($view === null) 
			$view = $this->view;
		
		$cd = $this->controller->definition($view, $id);
		$cd->childRelationId = Yii::app()->user->lastId;
		$this->controller->model = $cd->masterModel;
		
		$this->controller->render('ajaxFrameset', array(
				'model' => $cd->masterModel, // $this->controller->model, 
				'itemMenu' => strtolower($view),
				'sub' => $cd));		
	}
}