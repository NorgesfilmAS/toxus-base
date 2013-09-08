<?php
/**
 * showing a popup with a form
 * version 1.0 JvK 2013-09-06
 * 
 * writen for version 2.0 Bootstrap 3.0
 */
class DialogFormAction extends CAction
{
	/**
	 * the form to display to the user
	 * @var string
	 */
	public $modelClass = null;
	/**
	 * the name of the form to load
	 * @var string
	 */
	public $form;
	/**
	 * the template to render
	 * 
	 * @var string
	 */
	public $template = 'dialogForm';
	
	/**
	 * extra parameters that should be given to the template
	 * @var array
	 */
	public $params = array();
	
	public function run($id = null)
	{				
		if ($this->form == null) {
			Yii::log('The form is undefined', CLogger::LEVEL_WARNING, 'toxus.actions.DialogFormAction');
			return;			
		}
		$form = $this->controller->loadForm($this->form);
		if ($this->modelClass == null) {
			if (! isset($form['model'])) {
				Yii::log('The model is undefined', CLogger::LEVEL_WARNING, 'toxus.actions.DialogFormAction');
				return;			
			}		
			$this->modelClass = $form['model'];
		}	
		$this->controller->model = $this->controller->loadModel($id, $this->modelClass);
		$p = array_merge(
			$this->params, 
			array(
				'form' => $form,				
		));
		$this->controller->render($this->template, $p);
	}
					
}