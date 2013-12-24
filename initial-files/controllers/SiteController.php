<?php

Yii::import('toxus.controllers.BaseSiteController');

class SiteController extends BaseSiteController
{
	public function getPageTitle() {
		return $pageTitle = Yii::app()->params['appName'];
	}
	
	
	public function actions()
  {
		$c = Yii::app()->clientConfig;
		return array_merge(
			parent::actions(),			
			array(			

		));
	}	
}