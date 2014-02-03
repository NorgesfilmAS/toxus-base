<?php

/**
 * adjusted version of ClientScript so that jquery does not get loaded
 * twice
 */

Yii::import('toxus.extensions.minify.EClientScript');

class ClientScript extends EClientScript
{
	public $_isJqueryLoaded = false;

	/**
	 * Blocks jQuery from loading over and over again
	 * 
	 * @param string $name
	 */
	
	public function registerCoreScript($name, $force = false)
	{
		if ($force || $name != 'jquery' || $name=='jquery' && $this->_isJqueryLoaded == false ) {
			$this->_isJqueryLoaded = $this->_isJqueryLoaded || $name == 'jquery';
			parent::registerCoreScript($name);
		}
	}	
}