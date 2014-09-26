<?php

/**
 * mail handeling through postmark
 * 
 */


class MailPostmark extends MailMessage 
{
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
		$email = new ToxPostmark();
		
		if (is_array($msg['to'])) {
			foreach($msg['to'] as $to) {
				$email->addTo($to);
			}
		} else {
			$email->addTo($msg['to']);
		}
		$email->subject($msg['subject']);
		$email->messagePlain($msg['body']);
		
		if ($msg['cc']) {
			$email->addCC($msg['cc']);
		}
		if ($msg['bcc']) {
			$email->addBcc($msg['bcc']);
		}
		if ($msg['html']) {
			$email->addHtml($msg['html']);
		}
		try {
			if (Yii::app()->config->postmark['debug']) {
				$email->debug(Postmark::DEBUG_RETURN);
				$log['postmark'] = $email->send();
				$result = true;
			} else {
				$result = $msg->send();
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