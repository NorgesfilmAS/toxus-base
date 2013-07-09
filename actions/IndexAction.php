<?php

class IndexAction extends CAction
{
	public function run()
	{
		$controllerId = $this->controller->id;
		$s = ucFirst($controllerId);
		$this->controller->model = new $s;
		$this->controller->model->unsetAttributes();  // clear any default values
		if(isset($_GET[ucfirst($controllerId)]))
			$this->controller->model->attributes = $_GET[ucfirst($controllerId)];
		$this->controller->render('index', array('model' => $this->controller->model));		
		
	}
}