<?php

class BootstrapLinkPager extends CLinkPager
{
	public function init() {		
		$this->header = '<div style="text-align:right">'; // class="pagination">';
		$this->footer = '</div>';
		$this->cssFile = Yii::app()->baseUrl. '/css/toxus.css';
		$this->hiddenPageCssClass = 'hide-it';
		$this->htmlOptions['class'] = 'pagination';
		return parent::init();
	}   
}