<?php

return array(
	'title' => $this->te('login to your account'),	
	'model' => 'LoginForm',
	'action' => 'profile/login',	
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
			'label' => $this->te('btn-login'),	
		),
		'forgot_password' => array(
			'type' => 'link',
			'url'	 => $this->createUrl('forgot-password'),
			'label' => $this->t('forgot password', 1),	
		),
			
	),		
);