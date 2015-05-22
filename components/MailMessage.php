<?php
/**
 * Showing mail Messages
 * 
 */


class MailMessage extends BaseController
{
	
	/**
	 * the classname to log mailmessage to
	 * @var string|bool if false message are not logged
	 */
	public $mailLogModel = 'MailModel';

	public $errors = array();

	public function hasErrors() {
		return count($this->errors) > 0;
	}
	public function clearErrors() {
		$this->errors = array();
	}
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}
	/**
	 * 
	 * @param render a MailMessage $viewFile
	 * @param array $data data to be extracted and made available to the view
	 * @param array $params the parameters send to the render engine
	 */
	public function render($viewName, $data = array(), $return = false)
	{
		$viewFile = YiiBase::getPathOfAlias('application').'/'. $this->viewPath($viewName, array('directory' => 'mail'));
		if(($renderer = Yii::app()->getViewRenderer()) !== null && $renderer->fileExtension === '.'.CFileHelper::getExtension($viewFile))
			if (isset($data['controller'])) {
				$content = $renderer->renderFile($data['controller'], $viewFile, $data, true);
			} else {
				$content = $renderer->renderFile($this, $viewFile, $data, true);
			}
    else
      return false; // $content=$this->renderInternal($viewFile,$data,$return);
		
		if ($return)
			return $content;		
		
		return $this->sendEmail($content, $viewName);
	}
	
	/**
	 * Send a mail based on a template string
	 * 
	 * @param string $template the template to render
	 * @param BaseController $controller
	 * @param array $data
	 * @param boolean $return
	 * @return string / boolean
	 */
	public function renderString($template, $controller = null, $data = array(), $return = false)
	{
		$renderer = Yii::app()->viewRenderer;
		
		$content = $renderer->renderString(empty($controller) ? $this : $controller, $template, $data);
		if ($return) {
			return $content;			
		}
		return $this->sendEmail($content);
	}
	
	/**
	 * send a message to the user and logs it
	 * 
	 * @param type $to
	 * @param type $subject
	 * @param type $content
	 * @param type $data
	 * @return boolean 
	 */
	public function send($to, $subject, $content, $data = array())
	{
		$this->clearErrors();
		$log = array();
		$msg = 	array(
//					'from' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'],
					'to' => $to,
					'cc' => isset($data['cc'])? $data['cc'] : false,
					'bcc' => isset($data['bcc']) ? $data['bcc'] : false,	
					'subject' => $subject,
					'body' => str_replace(array_keys($data),array_values($data), $content),	
					'html' => isset($data['html']) ? str_replace(array_keys($data),array_values($data),$data['html']) : false,
					'attached' => array(),	
				);
		
		$log['parsed'] = $msg['body'];
		
		if (!$this->deliverMail($msg, $log)) { // mail($to, $subject, $body)) {
			$log['error'] = 'not send';		
			$this->errors['send'] = $msg['response'];
			$this->logMessage($msg, $log);
			return false;
		} else {
			$this->logMessage($msg, $log);
			return true;
		}	
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
		$filename = Yii::getPathOfAlias('application.vendors.'.$this->vendorViewRoot.'.mail.'.$viewName).$extension;
		if (is_file($filename))
			return $filename;

		throw new CException(Yii::t('base', 'The mail message {filename} does not exists', array('{filename}' => $filename)));
	}
	
	/**
	 * process the $content by it #tag to find the field for the mail message
	 * end sends it used the mailer
	 * 
	 * @param string $content
	 * @param path $viewName
	 * @return boolean message has been send (or just test send)
	 */
	protected function sendEmail($content, $viewName)
	{
		$log = array('viewName' => $viewName);

		$message = $this->parse($content);
		$msg = array_merge(
				array(
// 2015.02.23					'from' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'].'>',
					'to' => Yii::app()->params['editor'].'<'.Yii::app()->params['editor-email'].'>',
					'cc' => false,
					'bcc' => false,	
					'subject' => Yii::t('base', 'Message from {name}', array('name' => Yii::app()->params['company'])),
					'body' => '',	
					'html' => false,
					'attached' => array(),	
				),
				$message
		);		
		if (Yii::app()->config->mail['isBlocked']) {		
			$log['info'] = 'Mail not send: mail-blocked = true';
			$this->logMessage($msg, $log);
			return true; // just say it all worked
		}

		if (Yii::app()->config->mail['mailDomains']) {
			$to = $msg['to'];
			$mailDomains = explode(',', Yii::app()->config->mail['mailDomains']);
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
					$msg['to'] = Yii::app()->config->mail['collector'];				
					$log['reroute'] =  'Mail rerouted to admin ('.$to.')';
				} 
			}
		}	
		if (!$this->deliverMail($msg, $log)) {
			Yii::app()->user->setFlash('error', Yii::t('base', 'The message could not be send. Please try later again'));
			if (!isset($log['error'])) {
				$log['error'] = 'Message could not be send (server error)';
			}	
			$this->logMessage($msg, $log);
			return false;
		} else {
			$this->logMessage($msg, $log);
		}
		return true;		
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
		return mail($msg['to'], $msg['subject'], $msg['body']);
	}
	
	static function serverFromEmail($email)
	{
		$server = explode('@', $email);
		if (isset($server[1])) {
		  $s = explode('>', $server[1]);
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
			if (count($a) > 1 && strlen(trim($a[1])) > 0) {
				$message[$a[0]] = trim($a[1]);				
			}	
		}
		/* debug the mail message parsed */
		// $this->log = print_r($this->_message, true);
		return $message;
	}
	
	protected function logMessage($msg, $params=array())
	{
		if ($this->mailLogModel) {  
			$params['server'] = $_SERVER;
			$params['request'] = $_REQUEST;
			if (session_id() != '') {
				$params['session'] = $_SESSION;
			}	
			
			$mailClass = $this->mailLogModel;
			$mail = new $mailClass();
			if (isset($msg['to']))				{ $mail->to_address		= $msg['to']; }			
			if (isset($msg['subject']))		{ $mail->subject			= $msg['subject']; }
			if (isset($msg['body']))			{ $mail->message			= $msg['body']; }
			if (isset($msg['html']))			{ $mail->html_message = $msg['html']; }
			if (isset($msg['from']))			{ $mail->from_address = $msg['from']; }
			if (isset($msg['messageId'])) { $mail->message_id		= $msg['messageId']; }			
			if (isset($msg['errorCode'])) { $mail->error_code   = $msg['errorCode']; }
			if (isset($msg['response']))  { $mail->error_code   .= ' - '.$msg['response']; }
			if (isset($msg['errorCurl'])) { $mail->error_curl   = $msg['errorCurl']; }
			$mail->log = CJSON::encode($params);			
			$mail->save();
		}	
	}
	
}