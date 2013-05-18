<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

YiiBase::setPathOfAlias('toxus', dirname(dirname(__FILE__)).'/vendors/toxus');
return array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Toxus Application',
	'language' => 'en',		

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'toxus.extensions.giix-components.*', // giix components
		'toxus.components.*',	
		'toxus.models.*',					
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'2bad4u',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths' => array(
				'toxus.extensions.giix-core', // giix generators
			),								
		),		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,	
			'rules'=>array(
					/* needed to run gii in the wordpress enviroment */	
        'gii'=>'gii',
        'gii/<controller:\w+>'=>'gii/<controller>',
        'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',	
					
				'<controller:\w+>/<id:\d+>'=>'<controller>/index',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',				
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		// uncomment the following to use a MySQL database
		'db'=>array(
			// 'connectionString' => 'mysql:host=127.0.0.1;dbname=percussive_db',				
			'connectionString' => 'mysql:host=127.0.0.1;dbname=chordtrick_site',				
			'username' => 'chordtrick_site',	
			'emulatePrepare' => true,
			'password' => '!z11doen',
			'charset' => 'utf8',
		),
			
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
				),
				
			),
		),
		'template' => array(
			'class' => 'BaseTemplate',	
		),	
		'pageLog' => array(
			'class' => 'PageLog',	
		),	
		'viewRenderer' => array(
			'class' => 'TwigViewRenderer',
			'fileExtension' => '.twig',
			'options' => array(
					'autoescape' => true,
			),
			'extensions' => array(
					//'My_Twig_Extension',
			),
			'globals' => array(
					'html' => 'CHtml',
//				  'content' => 'ArticleController',
					'user' => 'Yii::app()->user',
			),
			'functions' => array(
					'rot13' => 'str_rot13',
					'file_exists' => 'file_exists',
			),
			'filters' => array(
					'jencode' => 'CJSON::encode',
			),
		),
			
			
		'config' => array(
			'class' => 'RuntimeConfig',	
		),	
	
		'clientScript' => array(
				'class' => 'toxus.extensions.minify.EClientScript',
				'combineScriptFiles' => false, // !YII_DEBUG, // By default this is set to true, set this to true if you'd like to combine the script files
				'combineCssFiles' => false, //!YII_DEBUG, // By default this is set to true, set this to true if you'd like to combine the css files
				'optimizeScriptFiles' => true, // !YII_DEBUG,	// @since: 1.1
				'optimizeCssFiles' => true, //!YII_DEBUG, // @since: 1.1
		),			
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'adminEmail'=>'info@chord-tricks.com',
		'company-server' => $_SERVER['SERVER_NAME'],	
		'company' => 'Matcho Brand Media',	
		'companyAddress' => 'Somewhere in the hard of The Netherlands',
		'companyEmail' => 'info@chord-tricks.com',
		'description' => 'Site to learn',	
		'debug' => true,	
		// if mail-blocked = true no mail is send	
		'mail-blocked' => false,
		// only these server are allowed to send to
		'mail-domains' => '', //'toxus.nl,example.com', 	
		// all other mail is send to this address	
		'mail-collector' => 'jaap@toxus.nl',	
			
		'userRoot' => YiiBase::getPathOfAlias('webroot.users'),			
		'image-large-filter' => array('fileTypes' => array('png','jpg','gif')),
		'editor' => 'Matthieu Brandt',
		'editor-email' => 'info@chord-tricks.com',	
			
		// pricing
		'price-basic' => '19.95',
		'price-extend' => '35.00',
		'coupon-2013' => '%50',
		'coupon-free' => '%100',	
	),
);