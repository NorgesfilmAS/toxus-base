<?php
/**
 * the configuration file used by the developer to set options
 * 
 */
return array(
	'security' => array(
		'label' => 'System security setup definitions',
		'items' => array(
			'password' => array(
				'value' => 'password',
				'info' => 'Password into system setup',
				/**
				 * states of isLocked:
				 *    - 0 : not locked
				 *    - 1 : locked from this level, so this level can unlock
				 *		- 2 : locked on lower level
				 */	
			),
		),
	),
	'db' => array(
		'label' => 'Database used by system',
		'items' => array(
			'connectionString' => array(
				'value' => 'mysql:host=127.0.0.1;dbname=pnek_rs4',
				'label' => 'Connection string',	
				'bound' => true,
			), 
			'username' => array(
				'value' => 'pnek_rs',		
				'bound' => true,	
			),
			'password' => array(
				'value' => '!z11doen',	
				'bound' => true,	
			),
		),
	),
	'debug' => array(
		'label' => 'System debug information',
		'items' => array(
			'firePHP' => array(
				'label' => 'Use FireBug',
				'type' => 'checkbox',
				'value' => 0,
			),
		),
	),
	'paypal' => array(
		'label' => 'Getting the connection to PayPal',
		'items' => array(	
			'apiPassword' => array(
					'label' => 'API password',
					'value' => 'thePayPalKey',
					'bound' => true,			// if changed reset this
			),		
		),			
	)	
);