<?php

class DeleteAction extends CAction
{
	public function run($id)
	{
		$controllerId = $this->controller->id;
		$modelClass = ucfirst($controllerId);
		$this->controller->model = $this->controller->loadModel($modelClass, $id);
		// what to do if the execute fails?
		if ($this->controller->executeDelete()) {
			$this->controller->redirect($this->controller->createUrl($controllerId.'/index'));
		}		
		$this->controller->redirect($this->controller->createUrl($controllerId.'/view', array('id' => $id)));		
	}
}