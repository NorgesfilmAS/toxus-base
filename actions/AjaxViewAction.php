<?php

class AjaxViewAction extends AjaxAction
{
	public function run($id)
	{
		$view = $this->view();
		$cd = $this->controller->subFrameDefinition($view, $id);		
		$cd->masterId = Yii::app()->user->lastId;
		
		$this->render('mainThreeColList', array(
				'model' => $this->model, 
//				'itemMenu' => 'course',
				'sub' => $cd)
		, false, true);				

	}
}