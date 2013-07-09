<?php
/**
 * update a record with the menu on the left site.
 * 
 */

class UpdateAction extends CAction
{
	public function run($id)
	{
		$controllerId = $this->controller->id;
		$this->controller->model = $this->controller->loadModel($id, ucfirst($controllerId));

		if (isset($_POST[ucfirst($controllerId)])) {
			if ($this->controller->executeUpdate()) {
				$this->controller->redirect($this->controller->createUrl($controllerId.'/view', array('id' => $id)));
			}
		}
		$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		$this->controller->render( 'view', array(
				'model' => $this->controller->model,
				'layout' => 'ajaxForm', 
				'form' => $form ));
		
	}
}