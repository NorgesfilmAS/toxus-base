<?php
/**
 * the definition of a page with a sub definition (list) and form/view combinatie.
 * All is loaded through Ajax
 * 
 * 
 */
class AjaxFrameDefinition extends CComponent
{
	/**
	 * the controller the frame is bound to
	 * @var CController
	 */
	public $controller = null;
	
	// urls to control the actions
	// the current key is -key-
	// if set to false the action is disabled. If set to '' it is autogenerated
	protected $_onEmptyUrl		= '';		// page to show if no items in the list. (empty clears div)
	protected $_onCreateUrl		= '';		// the create new. Add removes the add button
	protected $_onEditUrl			= '';		// empty hides the edit button
	protected $_onViewUrl			= '';		// empty stops hightlighting
	protected $_onRefreshUrl	= '';		// page to load when everything went well in $masterFrame
	
	/**
	 * there are 2 model in use: 
	 *   - masterModel - model for the page with the relation (childRelation) for the listview
	 *   - childModel -  model for the for the form/view with the relation back  (masterRelation) to the masterModel
	 */
	protected $_masterModelClass = '';
	protected $_masterModel = null;		// set this to overload the automated procedure
	public $masterId = null;
	public $childRelation =	'';
	
	protected $_childModelClass = '';
	protected $_childModel = null;    // set this on construction to overload the automatied process
	public $childId = null;
	public $masterRelation = '';
	
	public $childRelationId = null;						// if set masterModel->childRelation->id == $childId is highlighted
	
	/**
	 * The fields to use in the masterModel->childRelation to fill the list view
	 * 
	 */
	public $listIdField = 'id';
	public $listValueField = 'caption';
	public $sorted = true;	
	
	// default twig files to load working with the subController
	// defaults are: 
	//  - ajaxFrameset.twig  (the full frameset)
	//  - ajaxMenu.twig			 (the submenu)	
	//  - ajaxForm.twig      (when editing)
	//  - ajaxView.twig      (when viewing)
	// these can be overloaded by placing the overload file in the view/{controller} directory
	// for sections to overload look in the (type)Base.twig file.
	// 
	// 
	// text used in the Main Menu
	public $mainCaption = '';
	public $mainTypeText = '';

	// id of the div's to load. id-master is listview, id-slave it the form/view div
	public $masterFrame		= 'id-master';
	public $slaveFrame		= 'id-slave';
	
	/**
	 * 
	 * @param array of CActiveRecord $model to list in the view
	 */
	public function __construct($controller =null, $options = array()) {
		if ($controller == null) throw new CException('Controller is missing');
		$this->controller = $controller;
		
		foreach ($options as $key => $option) {
			$this->$key = $option;
		}
	}
	
	
	public function getMasterModel()
	{
		if ($this->_masterModel === null) {
			if ($this->masterId !== null) {
				$class = $this->_masterModelClass;
				$this->_masterModel = $class->model()->findByPk($this->masterId);
			} else if ($this->childModel !== null) {
				$relation = $this->masterRelation;
				$this->_masterModel = $this->childModel->$relation;
			}
		}
		return $this->_masterModel;
	}
	
	public function getChildModel()
	{
		if ($this->_childModel === null) {
			$class = $this->_childModelClass;
			if ($class === null) throw new CException('The _childModelClass is not defined');
			if ($this->childId == null) {			
				$this->_childModel = new $class;			
			} else {
				$this->_childModel = $class::model()->findByPk($this->childId);
			}
		}	
		return $this->_childModel;
	}
	
	
	public function getOnEditUrl()
	{
		if ($this->_onEditUrl === '') {
			$this->_onEditUrl = $this->controller->id.'/'.lcfirst($this->_masterModelClass).'Edit';
		}
		return $this->_onEditUrl;
	}
	
	public function getOnViewUrl()
	{
		if ($this->_onViewUrl === '') {
			$this->_onViewUrl = $this->controller->id.'/'.lcfirst($this->_masterModelClass).'View';
		}
		return $this->_onViewUrl;
	}
	public function getOnCreateUrl()
	{
		if ($this->_onCreateUrl === '') {
			$this->_onCreateUrl = $this->controller->id.'/'.lcfirst($this->_masterModelClass).'Create';
		}
		return $this->_onCreateUrl;
	}
	public function getOnRefreshUrl()
	{
		if ($this->_onRefreshUrl === '') {
			$this->_onRefreshUrl = $this->controller->id.'/'.lcfirst($this->_masterModelClass).'Refresh';
		}
		return $this->_onRefreshUrl;
	}
	
	
	/**
	 * return an array of id => value for the items found
	 * @return array
	 */
	public function getItems()
	{
		$relation = $this->childRelation;
		$listItems = $this->masterModel->$relation;
		
		$id = $this->listIdField;
		$value = $this->listValueField;
		$result = array();
		foreach ($listItems as $rec) {
			$result[$rec->$id] = $rec->$value;
		}
		if ($this->sorted)
			asort($result);
		return $result;
	}
	
	
}