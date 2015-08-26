<?php
/**
 * List in a grid style
 */

Yii::import('toxus.actions.BaseAction');

class ListAction extends BaseAction
{
	public $view = 'list';
	/**
	 * the name of the search-form file
	 * 
	 * @var string|bool false: there is no form
	 */
	public $form = false;
	
	public function run()
	{
		$this->checkRights();
		$modelClass = $this->modelName;
		
		if ($this->onCreateModel) {
      $id = false;
			call_user_func($this->onCreateModel, $id, $this);
		} else {	
			$this->controller->model = new $modelClass();
			$this->controller->model->unsetAttributes();  // clear any default values
		}	
		if(isset($_GET[$modelClass])) {
			$this->controller->model->attributes = $_GET[$modelClass];
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
		if ($this->form) {
			$params['form'] = $this->controller->loadForm($this->form);
		}
		$this->render($this->view, $params);
	}
}

