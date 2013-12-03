<?php

/**
 * the default definition of a Summery for the searching
 * 
 * it will render the search in the current directory
 */

class SearchSummery extends Summery
{
	/**
	 *
	 * @var string the view to render
	 */
	public $view = 'search';
	/**
	 *
	 * @var array the parameters for rendering
	 */
	public $parameter = null;
	
	public $width = 'col-sm-3  hidden-xs';	// default width, but hide on small screens
	
	public $layout = 'sidebar';
	
	public function init()
	{
		$this->caption = '';
	}
	
	/**
	 * render the current body
	 * 
	 * @param Controller $controller
	 */
	public function render($controller)
	{
		$this->addItem(array(
			'bodyHtml' => $controller->renderPartial($this->view, $this->parameter, true, true),	
		));
	}
}