<?php
/**
 * logging version of the PHP errors
 * 
 * version 1.0 JvK May 2014.
 * Uses the BaseDebugController to log PHP exceptions
 * 
 * Linked into system by changing the main.php to:
 * 'errorHandler'=>array(
 *		'class' => 'LogErrorHandler',
 *		'errorAction'=>'site/error',
 *    'logServer' => 'http://www.toxus.nl/api/debug/log',
 * 
 *	),
 *  
 */

class LogErrorHandler extends CErrorHandler 
{
	/*
	 * log to a fixed url (an other server)
	 */
	public $logUrl = false; // 'http://www.toxus.nl/api/debug/log'; // false;
	/**
	 * log to an url on the same server
	 * @var string
	 */
	public $logAction = 'site/log';	
	
	public $appIdent = false;				// the application running
	public $deviceIdent = false;		// the machine running on
	public $sessionIdent = false;		// session for user
	public $userId = false;					// id of the current user
	
	public function handle($event)
	{
		parent::handle($event);		// must be first because it analyses the stacktrace
		Yii::import('toxus.extensions.EHttpClient.*' );		
		if ($this->logUrl) {
			$url = $this->absoluteUrl($this->logUrl);
			$client = new EHttpClient($url, array(
				'maxredirects' => 0,
				'timeout'	=> 30,
			));			
		} elseif ($this->logAction && Yii::app()->controller) {
			$client = new EHttpClient(Yii::app()->controller->createAbsoluteUrl($this->logAction), array(
				'maxredirects' => 0,
				'timeout'	=> 30,
			));						
		} else { // logging is off
			return;
		}

		if ($event instanceof CExceptionEvent) {
			$e = $event->exception;
			$err['traces'] = $e->getTrace();
			$err['file'] = $e->getFile();
			$err['line'] = $e->getLine(); 
			$err['message'] = $e->getMessage();
		} else { // CErrorEvent
			$err = $this->error;
			$err['message'] = $event->message;
		}	
		$stack = $err['traces'];
		$state = array(
			'file' => $err['file'],
			'line' => $err['line']	
		);			
		$r = Yii::app()->request;
		$reply = array(
			'appId' => $this->appIdent,
			'deviceId' => $this->deviceIdent ? $this->deviceIdent : $r->userHostAddress,
			'sessionId' => $this->sessionIdent ? $this->sessionIdent : '(none)',
			'userId' => $this->userId ? $this->userId : Yii::app()->user->id,
			'typeId' => 0, //$err['type'],
			'errorUrl' => $r->requestUri,
			'errorMessage' => $err['message'],
			'cause' => ($event instanceof CExceptionEvent) ? 'exception' : 'error',
			'stackTrace' => $stack,
			'state' => $state,	
		);
		try {
			$response = $client->setRawData(CJSON::encode($reply, 'application/json'))->request('POST');
			if ($response->isSuccessful()) {
				$stack = $event;
			}			
		} catch (Exception $e) {
			
		}	
	}
	
	/**
	 * 
	 * @param string $url the url given
	 * @return string the absolute url
	 */
	private function absoluteUrl($url) 
	{
		if (substr($url,0,4) !== 'http') {
			if (Yii::app()->getUrlManager()->showScriptName) {
				return Yii::app()->getBaseUrl(true).'/index.php/'.$url;
			}
			return Yii::app()->getBaseUrl(true).'/'.$url;
		}
		return $url;
	}
}