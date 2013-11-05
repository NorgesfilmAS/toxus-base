<?php
/**
 * update a record with the menu on the left site.
 * 
 */

class UpdateAction extends CAction
{
	/*
	 * version 1.1: Introduced in PNEK 8/8/2013: $view and $edit defaults are changed to 
	 * viewForm so it will default load a page that can switch between edit and view
	 * 
	 */
	public $view = null;	// the view to open after the information has been saved successfully. must be the FULL path to the page
	public $edit = 'viewForm';	// the view to open to edit the current information
	public $form = null;		// the name of the form ex extension
	public $menuItem = null;// the menu item to active. Should be a jQuery selector (#menu-agent, or .agent-item)
	public $allowEdit = true;	// if false a 403 will be returned
	
	public function run($id, $mode='view')
	{
		if (!$this->allowEdit) {
			throw new CHttpException(403, 'Access denied');
		}
		$controllerId = $this->controller->id;
		$this->controller->model = $this->controller->loadModel($id, ucfirst($controllerId));
		$mode =  isset($_GET['mode']) ? $_GET['mode'] : 'view';
						
		if (isset($_POST[ucfirst($controllerId)])) {
			if ($this->controller->executeUpdate()) {
				/* this was in PNEK 
				if ($this->view == null) {
					$this->view = Yii::app()->baseURL.'/'.Yii::app()->request->pathInfo;
				}
				$this->controller->redirect($this->view);
				//$this->controller->redirect($this->controller->createUrl($controllerId.'/'.$this->view, array('id' => $id)));
			}
				 * 
				 */
				if (false && $this->view != null) {
					$this->controller->redirect($this->view);
				}
			}
			$mode = 'view';
			
		}
		if ($this->form == null)
			$form = $this->controller->loadForm($controllerId. 'Fields'); 				
		else 
			$form = $this->controller->loadForm($this->form);
		
		$this->controller->render( $this->edit, array(
				'model' => $this->controller->model,
				'layout' => 'ajaxForm', 
				'form' => $form,	
				'mode' => $mode,
				'state' => $mode,
				'menuItem' => $this->menuItem,
		));
		
	}
}