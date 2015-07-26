<?php

return array(
	'model' => 'UserProfile',
	'elements' => array(
		'passwordText' => array(
			'type' => 'password',
			'label' => Yii::t('base','Password'),
		),
		'passwordTextRepeat' => array(
			'type' => 'password',
			'label' => Yii::t('base','Retype your password')	
		),	
		'accepted_terms' => array(
			'type' => 'checkbox',
			'label' => 'I do accept the terms for using this website.'	
		),	
	),
	'buttons' => array(
		'ok' => $this->button( array(
				'type' => 'submit',
				'label' => Yii::t('base','set password'),
		)),	
	)	
);