<?php
/**
 * fix problems with the r quote
 */

class TinyMceBehavior extends CActiveRecordBehavior
{
	public $htmlFields = array();
	
	public function replaceChar($text)
	{
		return str_replace(array('&rsquo;'), array('\''), $text);
	}
	
	public function beforeSave($event) {
		foreach ($this->htmlFields as $field) {
			$this->owner->$field = $this->replaceChar($this->owner->$field);
		}
		return parent::beforeSave($event);
	}
	public function afterFind($event) {
		foreach ($this->htmlFields as $field) {
			$this->owner->$field = $this->replaceChar($this->owner->$field);
		}		
		return parent::afterFind($event);
	}
	public function beforeFind($event) {
		$s = '111111';
		return parent::beforeFind($event);
	}
}