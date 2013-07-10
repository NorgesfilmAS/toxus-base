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
	
	public $caption = '';		// caption to the view
	
	// urls to control the actions
	// the current key is -key-
	// if set to false the action is disabled. If set to '' it is autogenerated
	protected $_onEmptyUrl		= '';		// page to show if no items in the list. (empty clears div)
	protected $_onCreateUrl		= '';		// the create new. Add removes the add button
	protected $_onEditUrl			= '';		// empty hides the edit button
	protected $_onViewUrl			= '';		// empty stops hightlighting
	protected $_onDeleteUrl   = false;	
	protected $_onRefreshUrl	= '';		// page to load when everything went well in $masterFrame
	
	/**
	 * there are 2 model in use: 
	 *   - masterModel - model for the page with the relation (childRelation) for the listview
	 *   - childModel -  model for the for the form/view with the relation back  (masterRelation) to the masterModel
	 */
	public $masterModelClass = '';
	protected $_masterModel = null;		// set this to overload the automated procedure
	public $masterId; // = null;
	public $childRelation =	'';
	
	public $childModelClass = '';
	protected $_childModel = null;    // set this on construction to overload the automatied process
	public $childId = null;
	public $masterRelation = '';
	public $relationAttribute;				// the field that makes the relation between the master and the child
	
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

	// id of the div's to load. id-master is listview, id-slave it the form/view div
	public $masterFrame		= 'id-master';
	public $slaveFrame		= 'id-slave';
	
	/**
	 * the form for editing and default viewing
	 * default name is the master model class Fields: so
	 *   masterModelClass = 'Course' => filename = courseFields.php
	 */
	protected $_form = null;
	public $formName = '';
	
	public $isAjax = true;
	
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
				$class = $this->masterModelClass;
				$this->_masterModel = $class::model()->findByPk($this->masterId);
			} else if ($this->childModel !== null) {
				$relation = $this->masterRelation;
				$this->_masterModel = $this->childModel->$relation;
				$this->masterId = $this->masterModel->id;
			}
		}
		return $this->_masterModel;
	}
	
	public function getChildModel()
	{
		if ($this->_childModel === null) {
			$class = $this->childModelClass;
			if ($class === null) throw new CException('The childModelClass is not defined');
			if ($this->childId == null) {			
				$this->_childModel = new $class;			
			} else {
				$this->_childModel = $class::model()->findByPk($this->childId);
			}
		}	
		return $this->_childModel;
	}
	
	
	public function setUrl($url, $value)
	{
		$s = '_on'.ucfirst($url).'Url';
		$this->$s = $value;
	}
	public function getOnEditUrl()
	{
		if ($this->_onEditUrl === '') {
			$this->_onEditUrl = $this->controller->createUrl($this->controller->id.'/'.lcfirst($this->masterModelClass).'Edit', array('id' => '-key-'));
		}
		return $this->_onEditUrl;
	}

	
	public function getOnDeleteUrl()
	{
		if ($this->_onDeleteUrl === '') {
			$this->_onDeleteUrl = $this->controller->createUrl($this->controller->id.'/'.lcfirst($this->masterModelClass).'Delete', array('id' => '-key-'));
		}
		return $this->_onDeleteUrl;
	}
	
	public function getOnViewUrl()
	{
		if ($this->_onViewUrl === '') {
			$this->_onViewUrl = $this->controller->createUrl($this->controller->id.'/'.lcfirst($this->masterModelClass).'View', array('id' => '-key-'));
		}
		return $this->_onViewUrl;
	}
	public function getOnCreateUrl()
	{
		if ($this->_onCreateUrl === '') {
			$this->getMasterModel();	// to set the masterId always
			$this->_onCreateUrl = $this->controller->createUrl($this->controller->id.'/'.lcfirst($this->masterModelClass).'Create', array('id' => $this->masterId));
		}
		return $this->_onCreateUrl;
	}
	public function getOnRefreshUrl()
	{
		if ($this->_onRefreshUrl === '') {			
			$this->getMasterModel();	// to set the masterId always
			$this->_onRefreshUrl = $this->controller->createUrl($this->controller->id.'/'.lcfirst($this->masterModelClass).'Refresh', array('id' => $this->masterId));
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
		if ($listItems == null) return array();
		
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
	

	/**
	 * loads the form, if not already loaded
	 */
	public function getForm()
	{
		if ($this->_form === null and $this->formName !== false) {
			if ($this->formName == '') {
				$this->formName = lcfirst($this->masterModelClass).'Fields';				
			}
			if ($this->controller->model == null) {
				$this->controller->model = $this->masterModel;
			}
			$this->_form = $this->controller->loadForm($this->formName);
		}
		return $this->_form;
	}
}