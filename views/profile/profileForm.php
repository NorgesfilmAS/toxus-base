<?php

return array(
	'title' => Yii::t('base','Profile'),
	'model' => get_class(Yii::app()->user->profile),	
	'elements' => array(	
		'username' => array(
			'type' => 'text',			
		),
		'email' => array(
			'type' => 'text',			
		),
		'has_newsletter' => array(
			'type' => 'checkbox',	
		),	
	),
	'buttons' => 'default',	
);
