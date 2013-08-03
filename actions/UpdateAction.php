<?php
/**
 * update a record with the menu on the left site.
 * 
 */

class UpdateAction extends CAction
{
	public $view = 'view';	// the view to open after the information has been saved successfully
	public $edit = 'edit';	// the view to open to edit the current information
	public $form = null;		// the name of the form ex extension

	
	public function run($id)
	{
		$controllerId = $this->controller->id;
		$this->controller->model = $this->controller->loadModel($id, ucfirst($controllerId));

		if (isset($_POST[ucfirst($controllerId)])) {
			if ($this->controller->executeUpdate()) {
				$this->controller->redirect($this->controller->createUrl($controllerId.'/'.$this->view, array('id' => $id)));
			}
		}
		if ($this->form == null)
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		else 
			$form = $this->controller->loadForm($this->form);
		
		$this->controller->render( $this->edit, array(
				'model' => $this->controller->model,
				'layout' => 'ajaxForm', 
				'form' => $form,	
				'mode' => isset($_GET['mode']) ? $_GET['mode'] : 'view'	
		));
		
	}
}