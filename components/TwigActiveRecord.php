<?php
/**
 * fix for 'field not found' for fields that have a NULL value.
 * use this is unexpected error like: field not found, arrise
 */

class TwigActiveRecord extends GxActiveRecord
{
	public function __isset($name) {
	  if (! parent::__isset($name))		
			return isset($this->getMetaData()->columns[$name]);
		return true;
	}
}