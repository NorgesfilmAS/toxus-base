<?php
/**
 * base class for all page style definitions
 * 
 */
class BaseStyle extends CComponent
{

	public function setCurrent($stylename)
	{
		if ($stylename != __CLASS__) {
			$name = $stylename.'Style';
			Yii::app()->style = new $name();
		}	
	}
	
	public function getCurrent()
	{
		return __CLASS__;
	}

	/** 
	 * style options
	 */
	// defaults if style does not exist
	public function getView()
	{
		return 'index';
	}
	
	
}