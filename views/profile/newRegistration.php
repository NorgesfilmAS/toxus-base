<?php

return array(
	'title' => $this->te('create a new profile'),	
	'model' => 'LoginForm',	
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
		),	
		'has_newsletter' => array(
			'type' => 'checkbox',	
		)	
	),
	'buttons' => array(
		'submit' => array(
			'type' => 'submit',			
			'style' => 'btn-primary',	
			'label' => $this->te('btn-create'),	
		),
		'login' => array(
			'type' => 'link',
			'url'	 => $this->createUrl('profile/index'),
			'label' => $this->te('Sign in with an existing profile'),	
		),
			
	),		
);