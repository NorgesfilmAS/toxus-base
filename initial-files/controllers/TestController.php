<?php

class TestController extends Controller
{

	public function actionConvert()
	{
		echo getcwd();
		$filename = 'rsvideokunst.sql';
		$text = file_get_contents($filename.'.save');
		$text1 = utf8_decode($text);
		file_put_contents($filename.'.txt', $text1);
		Yii::app()->end();
	}
}