<?php

require_once(YiiBase::getPathOfAlias('toxus.vendors.extensions.Adapter_Interface'));

class Mail_Postmark_Adapter implements Mail_Postmark_Adapter_Interface
{
	public static $latestLogEntry;
	
	public static function getApiKey()
	{
		// return Mail_Postmark::TESTING_API_KEY;
		$key = Yii::app()->config->postmark['apiKey'];
		if (empty($key)) {
			throw new CException('The postmark["apiKey"] should be set');
		}
		return $key;
	}
	
	public static function setupDefaults(Mail_Postmark &$mail)
	{
		$from = Yii::app()->config->postmark['from'];
		$fromEmail = Yii::app()->config->postmark['fromEmail'];
		if (empty($from) || empty($fromEmail)) {
			throw new CException(Yii::t('base', 'The postmark[from] and postmark[fromEmail] should be set with setup'));
		}
		$mail->from($fromEmail, $from);
	}
	
	public static function log($logData)
	{
		self::$latestLogEntry = $logData;
	}
}


