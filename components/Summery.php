<?php
/**
 * Summery class file.
 *
 * @author Jaap van der Kreeft <jaap@toxus.nl>
 * @link http://www.toxus.nl/
 * @copyright 2008-2013 Toxus
 * @license http://www.toxus.nl/license/
 *
 */

class SummeryItem
{
	public $isHeader = false;
	public $caption = '';
	public $captionRight = false;
	public $url = false;
	public $bodyHtml = '';
}

class Summery extends CComponent
{
	/**
	 *
	 * @var string the caption of the summery
	 */
	protected $_caption = 'caption';
	
	/**
	 * the width of the column and other class styles
	 * @var string // if null sheet default is used
	 */
	public $width = null; 
	
	/**
	 * the layout of the column
	 * possible:
	 *  - ''				no special layout
	 *  - sidebar		sticky bar on the left
	 * @var string
	 */
	public $layout = null;
	
	/**
	 *
	 * @var string the text to display on the right side
	 */
	protected $_captionRight = false;
	
	/** 
	 * the item visible in this summery
	 * @var array
	 */
	public $items = array();
	
	
	public function getCaption()
	{
		return $this->_caption;
	}
	public function setCaption($value)
	{
		$this->_caption = $value;
	}
	public function getCaptionRight()
	{
		return $this->_captionRight;
	}
	public function getHasCaptionRight()
	{
		return $this->_captionRight !== false;
	}
	
	public function __construct() {
		$this->init();
	}
	public function init()
	{
	}
	/**
	 * add an item to the array
	 * 
	 * @param array / object / string $item
	 * @return boolean if the job has succeeded
	 */
	public function addItem($item)
	{
		if (is_array($item)) {
			$itm = new SummeryItem();
			$itm->isHeader = isset($item['isHeader']) && $item['isHeader'];
			$itm->caption = isset($item['caption']) ? $item['caption'] : '';
			$itm->captionRight = isset($item['captionRight']) ? $item['captionRight'] : false;
			$itm->url = isset($item['url']) ? $item['url'] : false;
			$itm->bodyHtml = isset($item['bodyHtml']) ? $item['bodyHtml'] : false;
			$this->items[] = $itm;			
		} elseif (is_a($item, 'SummeryItem' )) {
			$this->items[] = $item;
		} elseif (is_string($item)) {
			$itm = new SummeryItem();
			$itm->isHeader = true;
			$itm->caption = $item;
			$this->items[] = $itm;						
		} else {
			return false;
		}
		return true;
	}	
	
	/**
	 * render the current body of the summery
	 * 
	 * @param Controller $controller
	 */
	public function render($controller)
	{
	}
}