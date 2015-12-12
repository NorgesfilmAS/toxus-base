<?php

/**
 * mail handeling through postmark
 * 
 * version 2.0 Using the postmark templates
 * 
 */


class MailPostmark extends MailMessage 
{
  private $_apiKey = false;
    
  
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
  
  public function getApiKey() {
    if ($this->_apiKey === false) {
      $this->_apiKey = Yii::app()->config->mail['apiKey'];
      if (empty($this->_apiKey)) {
        throw new CException('The mail["apiKey"] should be set');
      }
    }
    return $this->_apiKey;
  }
  /**
	* Sends the e-mail. Prints debug output if debug mode is turned on
curl "https://api.postmarkapp.com/email/withTemplate" \
  -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Postmark-Server-Token: a63448b5-103c-432e-be8d-a2b946a13f45" \
  -d '{
  "From": "sender@example.com",
  "To": "recipient@example.com",
  "TemplateId": 262801,
  "TemplateModel": {
    "product_name": "product_name_Value",
    "name": "name_Value",
    "action_url": "action_url_Value",
    "username": "username_Value",
    "sender_name": "sender_name_Value",
    "product_address_line1": "product_address_line1_Value",
    "product_address_line2": "product_address_line2_Value"
  }
}'	
	*/
	public function sendWithTemplate($data)
	{
	
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'X-Postmark-Server-Token: ' . $this->apiKey
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email/withTemplate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$return = curl_exec($ch);
		$curlError = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		$this->_log(array(
			'messageData' => $data,
			'return' => $return,
			'curlError' => $curlError,
			'httpCode' => $httpCode
		));
		
		if ($curlError !== '') {
			throw new Exception($curlError);
		}
		
		if (!$this->_isTwoHundred($httpCode)) {
			if ($httpCode == 422) {
				$return = json_decode($return);
				throw new Exception($return->Message, $return->ErrorCode);
			} else {
				throw new Exception("Error while mailing. Postmark returned HTTP code {$httpCode} with message \"{$return}\"", $httpCode);
			}
		}
		
		if ($this->isDebug) {
			return array(
				'json' => json_encode($data),
				'headers' => $headers,
				'return' => $return,
				'curlError' => $curlError,
				'httpCode' => $httpCode
			);
		}
		
		return true;
	}
	  
  
  private function _log($info) {
    
  }
  
  	/**
	* If a number is 200-299
	*/
	private function _isTwoHundred($value)
	{
		return intval($value / 100) == 2;
	}

  /**
   * renders the mail with the postmark template engine
   * 
   * @param integer $templateId
   * @param array $params (From, To, TemplateModel)
   */
  // public function sendTemplate($templateId, $params) {
  public function sendTemplate($tempateId, $to, $model, $from=false) {
    $params = array(
      'TemplateId' => $tempateId,
      'From' => $from == false ? Yii::app()->config->mail['from'] : $from, // the system defined address
      'To' => $to,                 // fake address
      'TemplateModel' => $model,
    );
    return $this->sendWithTemplate($params);
  }
}