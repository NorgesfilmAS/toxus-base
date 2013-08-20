<?php

class ViewAction extends CAction
{
	const USE_LAST_ID = '99123459182782628372';
	
	public $view = 'view';
	public $form = null;		// the name of the form ex extension
	public $defaultMode = 'view';
	/**
	 * extra parameters merged for the view
	 * 
	 * @var array
	 */
	public $params = array();
	/**
	 * 
	 * @param type $id
	 */
	public function run($id=null)					
	{		
		if ($id == self::USE_LAST_ID)
			$id = Yii::app()->user->lastId;
		if ($id) {
			$this->controller->model = $this->controller->loadModel($id, ucfirst($this->controller->id));
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
					'form' => $form,	
					'mode' => isset($_GET['mode']) ? $_GET['mode'] : $this->defaultMode,					
				),
				$this->params
		);				
		$this->controller->render($this->view, $params);
	}
}