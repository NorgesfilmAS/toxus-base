<?php
/**
 * List in a grid style
 */

Yii::import('toxus.actions.BaseAction');

class ListAction extends BaseAction
{
	public $view = 'list';
	
	public function run()
	{
		$this->checkRights();
		
		$modelClass = $this->modelName;
		
		$model = new $modelClass();
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$modelClass])) {
			$model->attributes=$_GET[$modelClass];
		}	
		$this->controller->model = $model;
		$this->render($this->view, array('model' => $model));
	}
}

