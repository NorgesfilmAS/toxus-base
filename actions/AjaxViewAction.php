<?php

class AjaxViewAction extends AjaxAction
{
	/**
	 * 
	 * @param int $id the id of the item to show,not of the view.
	 */
	public function run($id)
	{
		
		$view = $this->view;
		$cd = $this->controller->definition($view, null, $id);	
		$cd->childRelationId = Yii::app()->user->lastId;
		
		$this->controller->render('ajaxView', array(
				'model' => $cd->childModel, 
				'sub' => $cd)
		, false, true);				

	}
}