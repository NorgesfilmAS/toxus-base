<?php

return array(
	'title' => $this->te('create a new profile'),	
	'model' => 'LoginForm',	
	'action' => 'profile/new',	
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
			'url'	 => $this->createUrl('/login'),
			'label' => $this->te('sign in with an existing profile.'),	
		),
			
	),		
);