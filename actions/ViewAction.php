<?php

class ViewAction extends CAction
{
	public function run($id)
	{		
		$this->controller->model = $this->controller->loadModel($id, ucfirst($this->controller->id));
		$this->controller->render('view', array('model' => $this->controller->model));
	}
}