<?php
/**
 * edit the profile of the user
 */
return array(
	'model' => 'User',
	'caption' => $this->te('user profile'),
	'elements' => array(
		'username' => array(
			'type' => 'text',
			'readOnly' => true,	
		),
		'fullname' => array(
			'type' => 'text',
		),			
		'password' => array(
			'type' => 'button',
			'caption' => $this->te('change password'),	
			'url' => $this->createUrl('site/profilePassword'),
			'isDialog' => true,	
			'hideLabel' => true,	
			'showMode' => 'view',	 // 'edit'	
			'formHidden' => true,	
		),	
/*			
 * 
 */
		'email' => array(
			'type' => 'email',	
		),
		'telephone' => array(
			'type' => 'string'
		),			
 		'address' => array(
			'type' => 'textarea'	
		),
	),
	'buttons' => 'default'	
);