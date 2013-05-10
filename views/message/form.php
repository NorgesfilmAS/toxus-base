<?php

return array(
	'title' => 'Reply to a message',	
	'model' => 'Message',	
	'elements' => array(	
		'previous' => array(
			'type' => 'view',
			'view' => '_previousMessage',	
		),
		'subject' => array(
			'type' => 'text',	
		),			
		'message' => array(
			'type' => 'textarea',	
			'class' => 'span,'	
		),
	),
	'buttons' => array(
		'submit' => array(
			'type' => 'submit',		
			'style' => 'btn-primary',	
		),
		'cancel' => array(
			'type' => 'cancel',	
		),	
	),		
);