<?php

return array(
	'title' => 'Profile',	
	'model' => 'UserProfile',	
	'elements' => array(
		'username' => array(),
		'password' => array(),
		'email' => array(
			'type' => 'email'	
		),
		'is_confirmed' => array(
			'type' => 'radiobuttonlist',
			'items'	=> $this->model->isConfirmedOptions()
		),
		'confirmMail' => array(
			'type' => 'button',
			'caption' => Yii::t('base','send mail'),
			'style' => 'btn-primary'	
		),	
		'rights_id' => array(
			'type' => 'dropdown',
			'items' => $this->model->rightsOptions,	
		),	
		'has_newsletter' => array(
			'type' => 'radiobuttonlist',
			'items'	=> Util::dataYesNo(),
		),
		'is_suspended' => array(
			'type' => 'radiobuttonlist',
			'items'	=> Util::dataYesNo(),
		),
		'newsletter_key' => array()	
	),		
	'buttons' => 'default',		
);