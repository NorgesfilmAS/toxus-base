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
	
	public function actionPayment()
	{
		$this->setPath();
		$a = new PaymentModelTest();
		$a->testRecalculate();
	}
	
}