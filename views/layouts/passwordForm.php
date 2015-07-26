<?php

return array(
	'title' => Yii::t('base','Reset your {productName} password' , array('{productName}' => Yii::app()->config->value('meta.productName'))),	
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
				'style' => 'btn btn-success',	
				'label' => Yii::t('base','Submit')	
			),
		),		
	),		
);