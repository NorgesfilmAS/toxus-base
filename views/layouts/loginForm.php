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
		'edit' => array(	
			'submit' => array(
				'default' => 'submit',	
				'type' => 'submit',			
				'position' => 'pull-right',	
				'label' => $this->te('btn-login'),	
				'style' => 'btn-primary',					
			),
			'forgot_password' => array(
				'type' => 'link',
				'url'	 => $this->createUrl('site/passwordRequest'),
				'label' => $this->t('forgot password?', 1),	
			),
		),		
	),		
);