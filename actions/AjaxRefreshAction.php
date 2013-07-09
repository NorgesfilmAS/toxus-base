<?php

class AjaxRefreshAction extends AjaxAction
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
		$cd = $this->controller->definition($view, $id);
		$cd->childRelationId = Yii::app()->user->lastId;
		
		$this->controller->model = $cd->masterModel;
		$this->controller->renderAjax('ajaxMenu', array(
			'model' => $cd->masterModel, // $this->controller->model, 
			'sub' => $cd));		
		
	}
}
	