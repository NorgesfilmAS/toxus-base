<?php
/**
 * show the summery page with a search bar, and 3 column loaded when needed
 * 
 * the controller can return information to the view by exposing 4 function
 * - search()
 * - column1()
 * - column2()
 * - column3()
 * 
 * version 1.0 Jvk 29 nov 2013
 */

Yii::import('toxus.actions.BaseAction');
class SummeryAction extends BaseAction
{
	public $view = 'summery';
	
	/**
	 * the definition of the columns. Every columns is Summery:
	 * 
	 * @var array
	 */
	public $columns = array();
	
	public function run()
	{
		$this->checkRights();
		
		foreach ($this->columns as $column) {
			if (is_a($column, 'Summery')) {
				$column->render($this->controller);
			}
		}
		$params = array(
			'columns' => $this->columns,	
		);
		
		$this->render($this->view, $this->mergeParams($params));
	}
}