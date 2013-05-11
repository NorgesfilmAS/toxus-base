<?php

return array(
	'title' => $this->te('lost or unknown password'),	
	'model' => 'LoginForm',	
	'elements' => array(	
		'email' => array(
			'type' => 'email',			
		),
	),
	'buttons' => array(
		'submit' => array(
			'type' => 'submit',			
			'style' => 'btn-primary',	
			'label' => $this->te('request password')	
		),
	),		
);