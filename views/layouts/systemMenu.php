<?php

return array(
	'home' => array(
		'label' => $this->t('home',1), 
		'url' => $this->createUrl('site/index'), 			
	),	
	'faq' => array(
		'label' => $this->t('FAQ', 1), 
		'url' => Yii::app()->baseUrl .'/faq', 
	),	
		
	'contact' => array(
		'label' => $this->t('contact',1), 
		'url' => $this->createUrl('/contact'), 			
	),	
		
);
