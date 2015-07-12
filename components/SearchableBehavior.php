<?php
/**
 * Makes a ActiveRecord searchable by the search.twig
 * 
 * version 1.0 jvk 2014-07-25
 * 
 */

class SearchableBehavior extends CActiveRecordBehavior
{
	public $searchOrder = false;	
	
	/**
	 * the fields to search on
	 * 
	 * all can be ommited except fieldname
	 * 
	 * @var array of array(
	 *		'fieldname', 
	 *		'label', 
	 *		'type',		: text | select
	 *		'data'		  key=>value pairs for the select
	 * )
	 */
	public $searchFields = array();
	public $searchExtraFields = array();
	/**
	 * list of fieldsname or array(
	 *	'ordername' => array(
	 *		attributes				: (require) comma list of fields and order to sort on
	 *		label						: (optional) name of the field
	 * ))
	 * @var array
	 */
	public $orderFields = array();
	
	/**
	 * the layout requested by the user
	 * @var string
	 */
	public $layout = 'grid'; // grid|wide|tiles
	
	/**
	 * set the items per page or false to use the default (itemsPerPage) amount
	 * @var integer|false
	 */
	public $itemsPerPage = false;
	
	/**
	 * there is no init, so we reuse the attach to modify the 
	 * orderFields to an all array version
	 * 
	 * @param type $owner
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		$orders = array();
		foreach ($this->orderFields as $key => $order) {
			if (!is_array($order)) {
				$orders[$order] = array('label' => $order, 'attributes' => $order);
			} else {
				$orders[$key] = $order;
			}
		}
		$this->orderFields = $orders;
		
		// add the searchOrder to the save fields, so we read it automatically
		$owner = $this->getOwner();
		$validators = $owner->getValidatorList();
		$validator = CValidator::createValidator('safe', $owner, 'searchOrder' );
		$validators->add($validator);
		$validator1 = CValidator::createValidator('safe', $owner, 'layout' );
		$validators->add($validator1);		
	}
					
	/**
	 * return the name of the class (for forms)
	 * 
	 * @return string
	 */
	public function getClassname()
	{
		return get_class($this->owner);
	}


	/**
	 * Overloaded version of the search with ordering
	 * 
	 * @return CActiveDataProvider
	 */
	public function searchOrder()
	{
		$search = $this->owner->search();		
		
		if (count($this->orderFields) > 0) { // only if we can order			
			if ($this->searchOrder == false || ! isset($this->orderFields[$this->searchOrder])) {		// not set by the form
				// searchOrder is the key in the orderFields or the value in the orderFields
				reset($this->orderFields);				
				$this->searchOrder = key($this->orderFields);			
			}
			$sort = new CSort();
			if ($this->searchOrder) {
				$a = $this->orderFields[$this->searchOrder]['attributes'];				
				$sort->defaultOrder = $a; //'default DESC';
			};					
			$search->sort = $sort;
		}	
		if ($this->itemsPerPage == false) {
			$items = explode(',', Yii::app()->config->setup['itemsPerPage']);
			$p = 10;
			foreach ($items as $item) {
				$a = explode(':', $item);
				if (isset($a[0]) && $a[0] == $this->layout) {
					$p = $a[1];
					break;
				}
			}
		} else {
			$p = $this->itemsPerPage;
		}	
		$search->pagination = array(
				'pageSize' => $p	
			);

		return $search;
	}
	
}