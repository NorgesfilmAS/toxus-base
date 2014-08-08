<?php

return array(
	'title' => 'Update article',
	'model' => 'ArticleModel',
	'elements' => array(
		'title' => array(
			'type' => 'string'	
		),
		'key' => array(
			'elements' => array(
				'key' => array(
					'type' => 'string',
					'width' => 'col-xs-3'	
				),
				'id' => array(
					'type' => 'panel',
					'readOnly' => false,	
					'width' => 'col-xs-3'							
				),	
				'footerOrder' => array(
					'type' => 'dropdown',
					'items' => $this->model->footerOrderOptions(),	
					'width' => 'col-xs-3'							
				)	
			),		
		),			
		'showGeneral' => array(
			'label' => Yii::t('base','visiblitiy'),
			'elements' => array(
				'showGeneral' => array(
					'type' => 'checkbox',
					'label' => 'All users',	
					'style' => 'checkbox-inline'	
				),
				'showModerator' => array(
					'type' => 'checkbox',							
					'style' => 'checkbox-inline',
					'label' => 'Moderators',							
				),	
					
				'showAdmin' => array(
					'type' => 'checkbox',							
					'style' => 'checkbox-inline',
					'label' => 'Admin',							
				),	
			)	
		),	
		'content' => array(
			'type' => 'html'	
		),	
	),
	'buttons' => 'default',	
);