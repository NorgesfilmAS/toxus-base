<?php

Yii::import('toxus.actions.BaseAction');

class ViewAction extends BaseAction
{
	const USE_LAST_ID = '99123459182782628372';
	
	public $view = 'viewForm';
	public $form = null;					// the name of the form ex extension
	public $defaultMode = 'view';
	/**
	 * user requested a special form of layout (like: grid, list, etc)
	 * @var string
	 */
	public $defaultUserLayout = '';
	public $menuItem = null;			// the menu item to active. Should be a jQuery selector (#menu-agent, or .agent-item)

	/**
	 * the default values to set for the new records
	 * @var false / array  [attrubite] => value
	 */
	public $modelDefaults = false;
	
	/**
	 * Default modelName holds the name of Controller class, but this can be 
	 * overwritten by setting the modelClass. 
	 * 
	 * ex:
	 *   modelClass    hasModel     result: uses model
	 *   null                       the controller class name            
	 *   false                      false 
	 *   'theModel'                 true  
	 * 
	 * @var boolean
	 */
//	public $hasModel = false;
	public function getHasModel()
	{
		return ! ($this->modelClass === false);
	}
	/**
	 * extra parameters merged for the view
	 * 
	 * @var array
	 */
	
	public function run($id=null, $layout=null)					
	{		
		$layoutSession = $this->view.'_layout';
		if ($layout != null) {
			Yii::app()->session[$layoutSession] = $layout;			
		}	else {
			$layout = isset(Yii::app()->session[$layoutSession]) ? Yii::app()->session[$layoutSession] : $layout;
		}
		
		if ($this->onCreateModel) {
			call_user_func($this->onCreateModel, $id, $this);
		} else {
			// $controllerId = ucfirst($this->controller->id);
			$modelName = $this->modelName;

			if ($id == self::USE_LAST_ID)
				$id = Yii::app()->user->lastId;
			if ($id && $this->hasModel) {
				$this->controller->model = $modelName::model()->findByPk($id); 
			} else if ($this->hasModel) {
				$this->controller->model = new $modelName();
				$this->controller->model->unsetAttributes();	// clear the defaults
				if ($this->modelDefaults) {
					foreach ($this->modelDefaults as $attribute => $value) {
						$this->controller->model->$attribute = $value;
					}
				}
			} else {
				$this->controller->model = null;				
			}
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
					'userLayout' => $layout,				// extra for user requested layout	
				),
				$this->params    
		);				
		$this->render($this->view, $params);
	}
}