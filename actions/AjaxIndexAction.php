<?php

class AjaxIndexAction extends AjaxAction
{
	
	public function run($id, $view=null)
	{
		if ($view === null) 
			$view = $this->view;
		
		$cd = $this->controller->subFrameDefinition($view, $id);
		$cd->masterId = Yii::app()->user->lastId;
		
		$this->controller->render('mainThreeColList', array(
				'model' => $this->controller, model, 
				'itemMenu' => strtolower($view),
				'sub' => $cd));		
	}
}