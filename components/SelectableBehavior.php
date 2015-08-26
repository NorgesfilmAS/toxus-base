<?php

class SelectableBehavior extends CActiveRecordBehavior
{
	public $formulaFields = array();
		
	public function translateField($field, $prefix = '') 
	{
		if (isset($this->formulaFields[$field])) {
			return str_replace('{prefix}', $prefix, $this->formulaFields[$field]);
		}
		return $field;
	}
	
}
