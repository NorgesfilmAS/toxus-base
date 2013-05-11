<?php

return array(
	'title' => 'login to your account',	
	'model' => 'LoginForm',	
	'elements' => array(	
		'username' => array(
			'type' => 'text',			
		),
		'password' => array(
			'type' => 'password',			
		),
		'rememberMe' => array(
			'type' => 'checkbox',	
		),	
	),
	'buttons' => array(
		'submit' => array(
			'type' => 'submit',			
			'style' => 'btn-primary',	
			'label' => 'Login'	
		),
		'forgot_password' => array(
			'type' => 'link',
			'url'	 => $this->createUrl('profile/password'),
			'label' => $this->t('forgot password', 1),	
		),
			
	),		
);