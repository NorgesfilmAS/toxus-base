<?php

class ViewAction extends CAction
{
	public $view = 'view';
	public $form = null;		// the name of the form ex extension
	
	public function run($id)
	{		
		$this->controller->model = $this->controller->loadModel($id, ucfirst($this->controller->id));
		$form = false;
				
		if (!empty($this->form)) {
			$form = $this->controller->loadForm($this->form);
		}
		$this->controller->render($this->view, array(
			'model' => $this->controller->model,
			'form' => $form,	
			'mode' => isset($_GET['mode']) ? $_GET['mode'] : 'view'	
		));
	}
}