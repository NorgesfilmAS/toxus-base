<?php

return array(
	'model' => 'UserProfile',
	'elements' => array(
		'passwordText' => array(
			'type' => 'password',
			'label' => $this->te('password'),
		),
		'passwordTextRepeat' => array(
			'type' => 'password',
			'label' => $this->te('again')	
		),	
		'accepted_terms' => array(
			'type' => 'checkbox',
			'label' => 'I do accept the terms for using this website.'	
		),	
	),
	'buttons' => array(
		'ok' => $this->button( array(
				'type' => 'submit',
				'label' => $this->te('set password'),
		)),	
	)	
);