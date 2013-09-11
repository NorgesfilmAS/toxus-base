<?php
/**
 * the editor to change fysical pages
 * version 1.0 2013-08-05
 * 
 * 
 */

return array(
	'model' => 'Page',	
		
	'elements' => array(	
		'page' => array(
			'type' => 'text',	
		),	
		'originalName' => array(
			'type' => 'hidden',	
		),	
		'content' => array(
			'type' => 'code',	
			'sourceType' => 'html',
			'hideLabel' => true,	
		),	
	),
	'buttons' => array(
		'edit' => array(	
			'submit' => array(
				'type' => 'submit',			
				'style' => 'btn-info',	
			),
			'cancel' => array(
				'type' => 'cancel',
			),	
		),
	),		
		
);