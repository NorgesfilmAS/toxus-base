<?php

return array(
	'title' => Yii::t('base','lost password'),	
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
				'label' => Yii::t('base','request password')	
			),
		),		
	),		
);