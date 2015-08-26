<?php


Yii::import('toxus.extensions.postmarkYii.Postmark');
class ToxPostmark extends Postmark
{
	/**
	 * the id of the message send
	 * 
	 * @var string 
	 */
	public $messageId = false;
	
	/**
	 * the error code return by sending the message
	 * @var integer
	 */
	public $errorCode = false;
	
	/**
	 * the response message after sending
	 * @var string
	 */
	public $response = false;
	
	/**
	 * the error return by the curl process
	 * @var string
	 */
	public $errorCurl = false;
	/**
	* Initialize
	*/
	public function __construct()
	{
		// return Mail_Postmark::TESTING_API_KEY;
		$key = Yii::app()->config->postmark['apiKey'];
		if (empty($key)) {
			throw new CException('The postmark["apiKey"] should be set');
		}
		
		$this->_apiKey = $key;

		$from = Yii::app()->config->postmark['from'];
		$fromEmail = Yii::app()->config->postmark['fromEmail'];
		if (!empty($from) || empty($fromEmail)) {
			$this->from($fromEmail, $from);
		}	
		$this->messageHtml(null)->messagePlain(null);
	}

	/**
	* Call the logger method, if one exists
	* 
	* @param array $logData
	*/
	protected function _log($logData) {		
		if (is_array($logData)) {
			if (isset($logData['return'])) {
				$result = CJSON::decode($logData['return']);								
				$this->messageId = isset($result['MessageID']) ? $result['MessageID'] : '';
				$this->errorCode = isset($result['ErrorCode']) ? $result['ErrorCode'] : false;
				$this->response = isset($result['Message']) ? $result['Message'] : '';
				$this->errorCurl = isset($logData['curlError']) ? $logData['curlError'] : '';
			}	
		}	else {
			throw new CException(Yii::t('base', 'Expecting log as array'));
		}
	}
	
}