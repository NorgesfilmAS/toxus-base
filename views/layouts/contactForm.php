<?php
/*
 * The basic template file for comments from the user.
 */
$a = array(
	'title' => Yii::t('base','contact'),
	'model' => 'Message',
	'action' => 'message/index',
	'controlWidth' => 'span12',	
	'elements' => array(	
		'name' => array(
			'type' => 'text'	
		),				
		'email' => array(
			'type' => 'email',			
		),
		'subject' => array(
			'type' => 'text',			
		),
		'message' => array(
			'type' => 'textarea'	
		)
	),
	'buttons' => array(
		'submit' => array(
			'type' => 'submit',			
			'style' => 'btn-info btn-large',	
			'label' => Yii::t('button','btn-submit'),	
		),
	),				
);
return $a;
		
