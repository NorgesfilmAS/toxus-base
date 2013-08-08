<?php

class ViewAction extends CAction
{
	const USE_LAST_ID = '99123459182782628372';
	
	public $view = 'view';
	public $form = null;		// the name of the form ex extension
	public $defaultMode = 'view';
	/**
	 * 
	 * @param type $id
	 */
	public function run($id)					
	{		
		if ($id == self::USE_LAST_ID)
			$id = Yii::app()->user->lastId;
		
		$this->controller->model = $this->controller->loadModel($id, ucfirst($this->controller->id));
		$form = false;
				
		if (!empty($this->form)) {
			$form = $this->controller->loadForm($this->form);
		}
		$this->controller->render($this->view, array(
			'model' => $this->controller->model,
			'form' => $form,	
			'mode' => isset($_GET['mode']) ? $_GET['mode'] : $this->defaultMode,	
		));
	}
}