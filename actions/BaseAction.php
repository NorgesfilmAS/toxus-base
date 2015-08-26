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
	 * @var string | null | false the class used to create a model, null => find it from the controller, false => no model to load
	 */
	public $modelClass = null;
	private $_modelName = null;
	
	/**
	 * the action to open redirect to on success 
	 * default is [controller->id/index]
	 * @var string
	 */
	public $successUrl = false;

	/**
	 * the layout of the curremt page
	 *  - full			(all 12 columns)
	 *  - content		(3 menu, 9 content)
	 *  - sub				(3 menu, 3 sub info, 6 content), 
	 *  - info			(3 menu, 6 content, 3 sub info)
	 * @var string
	 */
	public $pageLayout = 'content';		// 

	public $scenario = null;
	
	/**
	 *
	 * @var array the params to merge for the rendering
	 */
	public $params = array();
	
	
	/** 
	 * function called when model is created
	 * 
	 * @var array / string 
	 */
	public $onCreateModel = false;
	
	/**
	 * routine to call after the update is successfull
	 * 
	 * @var array
	 */
	public $onAfterUpdate = false;
	/** 
	 * the name of the model to create even if it is not defined by the calling routine
	 * 
	 * @return string
	 */
	/**
	 * the menu item to show
	 * @var string
	 */
	public $menuItem = null;			
	
  /** 
   * called after the model was create to adjust any setting of it.
   * @var array|false
   */
  public $afterLoadModel = false;
  
	protected function getModelName()
	{
		if ($this->_modelName === false) {
			return false;	// no model for this page
		}
		
		if ($this->_modelName === null) {
			if ($this->modelClass === null) {
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
	
	protected function mergeParams($params)
	{
		return array_merge(
						$params, 
						$this->params);
	}
}
