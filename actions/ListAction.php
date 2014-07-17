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
		
		if ($this->onCreateModel) {
			call_user_func($this->onCreateModel, $id, $this);
		} else {	
			$this->controller->model = new $modelClass();
			$this->controller->model->unsetAttributes();  // clear any default values
		}	
		if(isset($_GET[$modelClass])) {
			$this->controller->model->attributes=$_GET[$modelClass];
		}	
		
		$params = array_merge(
				array(
					'model' => $this->controller->model,
					'modelClass' => get_class($this->controller->model),	
					'menuItem' => $this->menuItem,						
					'layout' => $this->pageLayout,
				),
				$this->params    
		);				
		
		$this->render($this->view, $params);
	}
}

