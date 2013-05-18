<?php
/**
 * base class for all page style definitions
 * 
 */
class BaseTemplate extends CComponent
{

	public function init()
	{
		$b = 1;
	}
	
	public function setCurrent($stylename)
	{
		if ($stylename != __CLASS__) {
			Yii::import('application.templates.*');
			$name = ucfirst($stylename.'Template');
			Yii::app()->setComponent('template', array('class' => $name));
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
 	public function getScripts()
	{
		return array();
	}
 	public function getCss()
	{
		return array();
	}
	public function getTitleHidden()
	{
		return false;
	}
	
}