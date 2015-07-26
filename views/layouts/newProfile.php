<?php

return array(
	'title' => Yii::t('base','profile'),	
	'model' => 'UserProfile',	
	'action' => $this->createUrl('site/newProfile'),	
	'elements' => array(	
		'email' => array(
			'type' => 'email'	
		),				
		'username' => array(
			'type' => 'text',			
		),
		'password' => array(
			'type' => 'password',			
		),
		'passwordRepeat' => array(
			'type' => 'password',	
			'label' => Yii::t('base','type password again'),	
		),	
 		'has_newsletter' => array(
			'type' => 'checkbox',	
			'label' => Yii::t('base','subscribe to the newsletter'),
			'default' => 1,	
		)	
	),
	'buttons' => 'default'
);