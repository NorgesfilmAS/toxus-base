<?php
/**
 * default login form to show to the user
 */

return array(
	'title' => Yii::t('base', 'Login to your account'),	
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
				'label' => Yii::t('button', 'btn-login'),	
				'style' => 'btn btn-primary',					
			),
			'forgot_password' => array(
				'type' => 'link',
				'url'	 => $this->createUrl('site/passwordRequest'),
				'label' => Yii::t('button','btn-forgot-password', 1),
				'style' => 'btn btn-default btn-xs',
				'options' => 'style="margin-top:7px"',	
			),
		),		
	),		
);