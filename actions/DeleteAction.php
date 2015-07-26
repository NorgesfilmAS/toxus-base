<?php
/**
 * delete a record from the system
 * 
 */

Yii::import('toxus.actions.BaseAction');

class DeleteAction extends BaseAction
{
	
	
	public function run($id)
	{
		$this->checkRights();
		
		$modelClass = $this->modelName;		
		$model = new $modelClass();		
		
		$this->controller->model = $model::model()->findByPk($id);
		if (!$this->controller->model) {
			throw new CDbException('Id '.$id.' of '.$model.' could not be found');
		}
		
		if ($this->controller->executeDelete()) {
			$this->controller->redirect($this->successUrlFull);
		}		
		// show the user the error
		$this->render('formDialog',array(
			'title' => $this->controller->te('delete information'),
			'model' => $this->controller->model	
		));					
	}
}