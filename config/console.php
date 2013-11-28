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
		'db'=> include(dirname(__FILE__).'/local.db.php'),
			
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