<?php

class AjaxRefreshAction extends AjaxAction
{
	/**
	 * fills the listbox for an ajax call
	 * $view is the parent from which we are looking, which is the left menu definition	 
	 * $id is the record_id of the left, main menu
	 * 
	 * @param integer $id
	 * @param integer $showId the item to active
	 */
	public function run($id,$showId=null)
	{
		$view = $this->view;
		$cd = $this->controller->definition($view, $id);
		if ($showId == null) {
			$cd->childRelationId = Yii::app()->user->lastId;
		} else {
			$cd->childRelationId = $showId;
		}
		
		$this->controller->model = $cd->masterModel;
		$this->controller->renderAjax('ajaxMenu', array(
			'model' => $cd->masterModel, // $this->controller->model, 
			'sub' => $cd));		
		
	}
}
	