<?php

Yii::import('toxus.actions.BaseAction');

class ViewAction extends BaseAction
{
	const USE_LAST_ID = '99123459182782628372';
	
	public $view = 'viewForm';
	public $form = null;					// the name of the form ex extension
	public $defaultMode = 'view';
	public $menuItem = null;			// the menu item to active. Should be a jQuery selector (#menu-agent, or .agent-item)

	public $hasModel = false;
	/**
	 * extra parameters merged for the view
	 * 
	 * @var array
	 */
	
	public function run($id=null)					
	{		
		// $controllerId = ucfirst($this->controller->id);
		$modelName = $this->modelName;
		
		if ($id == self::USE_LAST_ID)
			$id = Yii::app()->user->lastId;
		if ($id) {
			$this->controller->model = $modelName::model()->findByPk($id); 
		} else if ($this->hasModel) {
			$this->controller->model = new $modelName();		
		} else {
			$this->controller->model = null;				
		}
		
		$form = false;				
		if (!empty($this->form)) {
			$form = $this->controller->loadForm($this->form);
		}
		$params = array_merge(
				array(
					'model' => $this->controller->model,
					'modelClass' => get_class($this->controller->model),	
					'form' => $form,	
					'mode' => isset($_GET['mode']) ? $_GET['mode'] : $this->defaultMode,
					'state' => 'view',	
					'menuItem' => $this->menuItem,						
					'layout' => $this->pageLayout,	
				),
				$this->params    
		);				
		$this->render($this->view, $params);
	}
}