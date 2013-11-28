<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
YiiBase::setPathOfAlias('toxus', dirname(dirname(__FILE__)).'/vendors/toxus');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'iCursus Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
		'db'=>array(
			//'connectionString' => 'mysql:host=www.icursus.com;dbname=icursus_site',				
			//'connectionString' => 'mysql:host=127.0.0.1;dbname=pnek_rs3',				
			'connectionString' => 'mysql:host=127.0.0.1;dbname=pnek_resource_space',					
				
			'username' => 'pnek_rs',	
			'emulatePrepare' => true,
			'password' => '!z11doen',
			'charset' => 'utf8',
		),
		//'db' => array(	
		'dbSystem' => array(			
			'connectionString' => 'mysql:host=127.0.0.1;dbname=pnek_site',				
			'username' => 'pnek_rs',	
			'emulatePrepare' => true,
			'password' => '!z11doen',
			'charset' => 'utf8',
			'class'  => 'CDbConnection'   	
		),
			
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
			
		'fixture' => array(
			'class' => 'system.test.CDbFixtureManager',	
		),			
	),		
	'commandMap' => array(
		'fixture' => array(
				'class' => 'toxus.extensions.fixtureHelper.FixtureHelperCommand',
		)	
	),			
);