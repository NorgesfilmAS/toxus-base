<?php

class BaseElFinderController extends CController
{
	public function actions()
	{
			return array(
					'connector' => array(
							'class' => 'toxus.extensions.elFinder.ElFinderConnectorAction',
							'settings' => array(
									'root' => Yii::getPathOfAlias('webroot'), // . Article::USER_ROOT,
									'URL' => Yii::app()->baseUrl, // . Article::USER_ROOT,
									'rootAlias' => 'Home',
									'mimeDetect' => 'none'
							)
					),
			);
	}
}