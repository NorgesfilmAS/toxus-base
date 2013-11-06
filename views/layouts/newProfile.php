<?php

return array(
	'title' => $this->te('profile'),	
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
			'label' => $this->te('type password again'),	
		),	
 		'has_newsletter' => array(
			'type' => 'checkbox',	
			'label' => $this->te('subscribe to the newsletter'),
			'default' => 1,	
		)	
	),
	'buttons' => 'default'
		/*
		array(
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
		 * 
		 */
		
);