<?php

class MailMessage extends CBaseController
{
	
	/**
	 * 
	 * @param render a MailMessage $viewFile
	 * @param array $data data to be extracted and made available to the view
	 * @param array $params the parameters send to the render engine
	 */
	public function render($viewName, $data = array(), $return = false)
	{
		$viewFile = $this->getViewFile($viewName);
		if(($renderer = Yii::app()->getViewRenderer()) !== null && $renderer->fileExtension === '.'.CFileHelper::getExtension($viewFile))
      $content = $renderer->renderFile($this, $viewFile, $data, true);
    else
      return false; // $content=$this->renderInternal($viewFile,$data,$return);
		
		if ($return)
			return $content;		
		
		return $this->sendEmail($content, $viewName);
	}
	
	/**
	 * get the fysical name of the or false
	 * 
	 * @param string $viewName	 
	 * 
	 */
	public function getViewFile($viewName) {
		// check if
		if (($renderer = Yii::app()->getViewRenderer()) !== null)
        $extension = $renderer->fileExtension;
    else
        $extension = '.php';
		$filename = Yii::getPathOfAlias('application.views.mail.'.$viewName).$extension;
		if (is_file($filename))
			return $filename;
		throw new CException('The message '.$filename.' does not exists');
	}
	
	protected function sendEmail($content, $viewName)
	{
		$log = array('viewName' => $viewName);
		
		$default = array(
			'from' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'],
			'to' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'].'>',
			'subject' => Yii::t('app', 'Message from {name}', array('name' => Yii::app()->params['company'])),
			'body' => '',	
		);

		$message = $this->parse($content);
		$msg = array_merge(
				array(
					'from' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'],
					'to' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'],
					'subject' => Yii::t('app', 'Message from {name}', array('name' => Yii::app()->params['company'])),
					'body' => '',	
				),
				$message
		);		
		if (isset(Yii::app()->params['mail-blocked']) && Yii::app()->params['mail-blocked'] === true) { // no send just log the message
			$log['info'] = 'Mail not send: mail-blocked = true';
			$this->logMessage($msg, $log);
			return true;
		}

		if (Yii::app()->params['mail-domains']) {
			$to = $msg['to'];
			$mailDomains = split(',', Yii::app()->params['mail-domains']);
			if (count($mailDomains) > 0) {
				$toServer = self::serverFromEmail($to);
				$isAllowed = false;
				foreach ($mailDomains as $domain) {
					if ($toServer == trim($domain)) {
						$isAllowed = true;
						break;
					}
				}			
				if (! $isAllowed) {
					$msg['to'] = Yii::app()->params['mail-collector'];				
					$log['reroute'] =  'Mail rerouted to admin ('.$to.')';
				} 
			}
		}	
		if (!mail($msg['to'], $msg['subject'], $msg['body'])) {
			Yii::app()->user->setFlash('error', 'The message could not be send. Please try later again');
			$log['error'] = 'Message could not be send (server error)';
			$this->logMessage($msg, $log);
			return false;
		} else {
			$this->logMessage($msg, $log);
		}
		return true;
	}
	
	static function serverFromEmail($email)
	{
		$server = split('@', $email);
		if (isset($server[1])) {
		  $s = split('>', $server[1]);
			if (isset($s[0]))
				return $s[0];
		}
		return null;
	}
	
	
	/**
	 * parses the message and return an array of element
	 * 
	 * @param string $text 
	 */
	public function parse($text = null)
	{
		$message = array();
		$textElements = explode("\n#", "\n".$text);
		foreach ($textElements as $textElement ) { // textElement = from: jaap van der Kreeft
			$a = explode(':', $textElement, 2);
			$message[$a[0]] = trim($a[1]);				
		}
		/* debug the mail message parsed */
		// $this->log = print_r($this->_message, true);
		return $message;
	}
	
	protected function logMessage($msg, $params=array())
	{
		$params['server'] = $_SERVER;
		$params['request'] = $_REQUEST;
		$params['session'] = $_SESSION;
		$params['message'] = $msg;
		$mail = new Mail();
		$mail->log = var_export($params, true);
		$mail->message = $msg['body'];
		$mail->save();
	}
	
}