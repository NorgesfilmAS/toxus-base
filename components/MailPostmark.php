<?php

/**
 * mail handeling through postmark
 * 
 */


class MailPostmark extends MailMessage 
{
  private $_version = 1;
  public function __construct($version = 1) {
    $this->_version = $version;
    parent::__construct();
  }
  
  public function getIsDebug() {
    if ($this->_version == 1) {
      return Yii::app()->config->postmark['debug'];
    } else {
      return Yii::app()->config->mail['debug'];
    }
  }
	/**
	 * Send the mail to the user 
	 * @param array $msg
	 *		- from
	 *		- to
	 *		- cc
	 *		- bcc
	 *		- subject
	 *		- body
	 *		- html
	 *		- attached
	 * @return bool true if send was successfull
	 */  
	protected function deliverMail(&$msg, &$log)
	{
		$email = new ToxPostmark($this->_version);
		
		if (is_array($msg['to'])) {
			foreach($msg['to'] as $to) {
				$email->addTo($to);
			}
		} else {
			$email->addTo($msg['to']);
		}
		$email->subject($msg['subject']);
		$email->messagePlain($msg['body']);
		if (isset($msg['from'])) {
			$email->from($msg['from'], isset($msg['fromName']) ? $msg['fromName']:  null);
		}
		
		if ($msg['cc']) {
			$email->addCC($msg['cc']);
		}
		if ($msg['bcc']) {
			$email->addBcc($msg['bcc']);
		}
		if ($msg['html'] && $msg['html'] != 'html') {
			$email->messageHtml(trim($msg['html']));
		}
		try {
      if ($this->isDebug) {
			
				$email->debug(Postmark::DEBUG_RETURN);
				$log['postmark'] = $email->send();
				$result = true;
			} else {
				$result = $email->send();
			}
		} catch (Exception $e) {
			$result = false;
		}	
		$msg['messageId'] = $email->messageId;
		$msg['errorCode'] = $email->errorCode;
		$msg['response'] = $email->response;
		$msg['errorCurl'] = $email->errorCurl;
		return $result;
	}	
}