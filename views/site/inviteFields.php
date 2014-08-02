<?php

return array(
	'model' => 'UserProfile',
	'elements' => array(
		'passwordText' => array(
			'type' => 'password',
			'label' => Yii::t('base','password'),
		),
		'passwordTextRepeat' => array(
			'type' => 'password',
			'label' => Yii::t('base','again')	
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