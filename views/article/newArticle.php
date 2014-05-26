<?php

return array(
		'title' => 'New article',
		'model' => 'ArticleModel',
		
		'elements' => array(
				'title' => array(
					'type' => 'string'	
				),
				'key' => array(
					'type' => 'string'	
				),
				'className' => array(
					'type' => 'string'	
				), 
				'content' => array(
					'type' => 'html'	
				),
				'footerOrder' => array(
					'type' => 'dropdown',
					'items' => $this->model->footerOrderOptions()	
				)
		),
		'buttons' => 'default'
);