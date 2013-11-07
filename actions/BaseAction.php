<?php
/* 
 * the base of all modern action
 * 
 */
class BaseAction extends CAction
{
	/** the view to render */
	public $view = 'index';
	/**
	 *
	 * @var boolean: returns a 403 if false
	 */
	public $allowed = true;
	public $notAllowedMsg = 'access denied';
	
	/**
	 *
	 * @var string the class used to create a model
	 */
	public $modelClass = null;
	private $_modelName = null;
	
	/**
	 * the action to open redirect to on success 
	 * default is [controller->id/index]
	 * @var string
	 */
	public $successUrl = null;
	
	/** 
	 * the name of the model to create even if it is not defined by the calling routine
	 * 
	 * @return string
	 */
	protected function getModelName()
	{
		if ($this->_modelName == null) {
			if ($this->modelClass == null) {
				$controllerId = $this->controller->id;
				$this->modelClass = ucfirst($controllerId);
			}
			$this->_modelName = $this->modelClass;
		}
		return $this->_modelName;
	}
	
	protected function getSuccessUrlFull()
	{
		if ($this->successUrl == null) {
			$this->successUrl = $this->controller->createUrl($this->controller->id.'/index');
		}
		return $this->successUrl;
	}	
	/** 
	 * 
	 * @throws CHttpException when user does not have the rights
	 */
	protected function checkRights()
	{
		if (!$this->allowed)
			throw new CHttpException(403, $this->controller->te($this->notAllowedMsg));
		return true;
	}
	
	public function render($view,$data=null,$return=false)
	{
		return $this->controller->render($view, $data, $return);
	}
}
