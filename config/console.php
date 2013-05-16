<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.

YiiBase::setPathOfAlias('toxus', dirname(dirname(__FILE__)).'/vendors/toxus');
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Percussive Guitar Console',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
		'db'=>array(			
			// 'connectionString' => 'mysql:host=www.chord-tricks.com;dbname=chordtrick_site',		
			'connectionString' => 'mysql:host=127.0.0.1;dbname=chordtrick_site',				
			'username' => 'chordtrick_site',	
				
			'username' => 'percussive_db',	
			'emulatePrepare' => true,
			'password' => '!z11doen',
			'charset' => 'utf8',
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