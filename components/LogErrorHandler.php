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
	public $logUrl = 'http://www.toxus.nl/api/debug/log'; // false;
	public $appIdent = false;				// the application running
	public $deviceIdent = false;		// the machine running on
	public $sessionIdent = false;		// session for user
	public $userId = false;					// id of the current user
	
	public function handle($event)
	{
		parent::handle($event);		// must be first because it analyses the stacktrace
		
		if ($this->logUrl) {	// logging is active
			Yii::import('toxus.extensions.EHttpClient.*' );
			$client = new EHttpClient($this->absoluteUrl($this->logUrl), array(
				'maxredirects' => 0,
				'timeout'	=> 30,
			));
			if ($event instanceof CExceptionEvent) {
				$err = $this->exception;
			} else { // CErrorEvent
				$err = $this->error;
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
				'errorMessage' => $event->message,
				'cause' => ($event instanceof CExceptionEvent) ? 'exception' : 'error',
				'stackTrace' => $stack,
				'state' => $state,	
			);
			$response = $client->setRawData(CJSON::encode($reply, 'application/json'))->request('POST');
			if ($response->isSuccessful()) {
				$stack = $event;
			}
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