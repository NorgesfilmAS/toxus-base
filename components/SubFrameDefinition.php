<?php

class SubFrameDefinition extends CComponent
{
	/**
	 * the page.twig to load initial load in the masterFrame. Default = _subMenuFrame
	 * @var string
	 */
	public $masterFramePage;
	public $masterFrame		= 'id-master';
	public $slaveFrame		= 'id-slave';
	
	// urls to control the actions
	// the current key is -key-
	public $onEmptyUrl		= '';		// page to show if no items in the list. (empty clears div)
	public $onCreateUrl		= '';		// the create new. Add removes the add button
	public $onEditUrl			= '';			// empty hides the edit button
	public $onViewUrl			= '';			// empty stops hightlighting
	public $onRefreshUrl	= '';	// page to load when everything went well in $masterFrame
	
	// the data shown in the list
	public $model = null;
	public $listIdField = 'id';
	public $listValueField = 'caption';
	public $sorted = true;
	public $masterId = null;		// which id is default visible. null means the first one.
	
	public $isAjax = true;
	
	// default twig files to load working with the subController
	public $editPage = '';
	public $viewPage = '';
	
	// text used in the Main Menu
	public $mainCaption = '';
	public $mainTypeText = '';
	
	// protected because they are automated to
	protected $_modelClass = false;		// default get_class($model)
	
	/**
	 * 
	 * @param array of CActiveRecord $model to list in the view
	 */
	public function __construct($model = null, $options = array()) {
		$this->model = $model;
		foreach ($options as $key => $option) {
			if (isset($this->$key)) {
				$this->$key = $option;
			} else {
				throw new CException('Unknown option: '.$key);
			}
		}
	}
	
	
	/**
	 * return an array of id => value for the items found
	 * @return array
	 */
	public function getItems()
	{
		if ($this->model === null) return array();
		$id = $this->listIdField;
		$value = $this->listValueField;
		$result = array();
		foreach ($this->model as $rec) {
			$result[$rec->$id] = $rec->$value;
		}
		if ($this->sorted)
			asort($result);
		return $result;
	}
	
	public function getModelClass()
	{
		if ($this->_modelClass)
			return $this->_modelClass;
		return get_class($this->model);	
	}
}