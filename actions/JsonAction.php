<?php

/**
 * action that return it's information as a json string
 * 
 * 
 */

class JsonAction extends CAction
{
	/**
	 * the scenario used to load the parameters
	 * 
	 * @var string
	 */
	public $scenario = 'edit';
	
	
	public function __construct($controller,$id)
	{
		if (!($controller instanceof BaseJsonController)) {
			throw new CException('JsonAction always needs a JasonController');
		}
		parent::__construct($controller, $id);
	}
	
	/**
	 * return the model connected to the controller
	 * 
	 * @return CActiveRecord
	 */
	public function getModel()
	{
		return $this->controller->model;
	}
	
	
	
}