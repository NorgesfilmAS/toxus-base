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
	),		
	'buttons' => 'default',		
);