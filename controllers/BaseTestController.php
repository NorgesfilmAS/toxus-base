<?php

class BaseTestController extends Controller
{
	private function setPath()
	{
		Yii::import('system.test.CTestCase');
		Yii::import('system.test.CDbTestCase');
		Yii::import('system.test.CWebTestCase');
		Yii::import('application.tests.unit.*');
		Yii::setPathOfAlias('phpunit', '/usr/lib/php');
		Yii::import('phpunit.*');		
	}
	
	public function actionArticle()
	{
		$this->setPath();
		$a = new ArticleModelTest();
		$a->testEditor();
	}
	
	public function actionPayment()
	{
		$this->setPath();
		$a = new PaymentModelTest();
		$a->testRecalculate();
		$a->testCouponActive();
	}
	
	public function actionProvider()
	{
		$this->setPath();
		$a = new PaymentProviderTest();
		$a->testBase();
	}
	
}