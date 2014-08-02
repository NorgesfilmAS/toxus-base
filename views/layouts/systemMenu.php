<?php

return array(
	'home' => array(
		'label' => Yii::t('base', 'home',1), 
		'url' => $this->createUrl('site/index'), 			
	),	
	'faq' => array(
		'label' => Yii::t('base','FAQ', 1), 
		'url' => Yii::app()->baseUrl .'/faq', 
	),	
		
	'contact' => array(
		'label' => Yii::t('base','Contact',1), 
		'url' => $this->createUrl('/contact'), 			
	),	
		
);
