<?php

class AjaxListAction extends AjaxAction
{
	/**
	 * fills the listbox for an ajax call
	 * $view is the parent from which we are looking, which is the left menu definition	 
	 * $id is the record_id of the left, main menu
	 * 
	 * @param string $view
	 * @param integer $id
	 */
	public function run($id )
	{
		$view = $this->view;
		$cd = $this->controller->subFrameDefinition($view, $id);
		$cd->masterId = Yii::app()->user->lastId;
		$this->controller->renderAjax('_subMenuFrame', array(
			'model' => $this->controller->model, 
			'sub' => $cd));		
		
	}
}
	