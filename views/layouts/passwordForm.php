<?php

return array(
	'title' => $this->te('lost password'),	
	'model' => 'LoginForm',	
	'elements' => array(	
		'email' => array(
			'type' => 'email',			
		),
	),
	'buttons' => array(
		'edit' => array(	
			'submit' => array(
				'type' => 'submit',			
				'style' => 'btn btn-primary',	
				'label' => $this->te('request password')	
			),
		),		
	),		
);