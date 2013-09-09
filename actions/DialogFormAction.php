<?php
/**
 * showing a popup with a form
 * version 1.0 JvK 2013-09-06
 * 
 * writen for version 2.0 Bootstrap 3.0
 * 
 * The call to the dialog is ALWAYS an ajax call. So if everything went ok, ok is returned
 */
class DialogFormAction extends CAction
{
	/**
	 * the form to display to the user
	 * @var string
	 */
	public $modelClass = null;
	/**
	 * the name of the field in $modelClass to set the given id, when creating a new record
	 * 
	 * @var string
	 */
	public $masterField = null;
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
	 * the url to open after the form has been succesfully dismissed
	 * 
	 * @var string
	 */
	public $url = null;
	/**
	 * extra parameters that should be given to the template
	 * @var array
	 */
	public $params = array();
	
	/**
	 * 
	 * @param integer $id the id of the record or the id of the parent to connect to
	 * @param boolean $isId if set to 1 the id represent the id of the record, if 0 the id of the parent
	 * @return string
	 */
	public function run($id = null, $isId = true)
	{				
		if ($this->modelClass == null) {
			Yii::log('The model is undefined', CLogger::LEVEL_WARNING, 'toxus.actions.DialogFormAction');
			return;			
		}	
		if ($id == null) {
			$m = $this->modelClass;
			$this->controller->model = new $m;			
		} elseif ($isId == false ) {
			$m = $this->modelClass;
			$this->controller->model = new $m;
			if ($this->masterField) {
				$f = $this->masterField;
				$this->controller->model->$f = $id;
			}
		} else {
			$this->controller->model = $this->controller->loadModel($id, $this->modelClass);
		}	
		
		if ($this->form == null) {
			Yii::log('The form is undefined', CLogger::LEVEL_WARNING, 'toxus.actions.DialogFormAction');
			return;			
		}
		$form = $this->controller->loadForm($this->form);
		
		if (isset($_POST[$this->modelClass])) {
			$this->controller->model->attributes = $_POST[$this->modelClass];
			if ($this->controller->model->save()) {
				$result = array(
					'status' => '200',
					'url' => str_replace('--key--',$this->controller->model->id, $this->url),	
				);
				echo CJSON::encode($result);
				Yii::app()->end(200);
			}
		}
		
		$p = array_merge(
			$this->params, 
			array(
				'form' => $form,				
		));
		$this->controller->render($this->template, $p);
	}
					
}