<?php
/**
 * Yii interface to MailChimp
 * 
 * version 0.01 JvK 2014.08.04
 * 
 * uses setup-local.mailchimp:
 *    * apiKey
 * 
 */
require_once(dirname(__FILE__).'/api/src/Mailchimp.php');

class ToxusMailchimp extends CComponent
{
	private $_apiKey = false;
	private $_mailchimp = false;
	public $errors = array();	
	
	/**
	 * subscribes one email to an list
	 * 
	 * @param string $email
	 * @param array $fields array of key=>values to store by the user
	 * @param string $listId
	 * @param array $options
	 * @return boolean | array false on error (errors is set with the error)
	 *     - email:				the address subscribed
	 *     - userId:			unique mailchimp id
	 *     - userListId : id of the user in the list
	 */
	public function subscribe($email, $fields=array(), $listId=false, $options = array())
	{
		
		
		$this->errors = array();
		$defaults = array_merge(
			array(
				'isHtml' => true,	
				'doubleOptIn' => $this->doubleOptIn,
				'updateExisting' => true,
				'sendWelcome' => false,	
			),			
			$options
		);
		if ($listId === false) {
			$listId = Yii::app()->config->mailchimp['list'];
		}
		$response = $this->mailchimp->lists->subscribe(
						$listId,
						array('email' => $email),
						$fields,
						$defaults['isHtml'] ? 'html' : 'text',						
						$defaults['doubleOptIn'],				
						$defaults['updateExisting'],
						$defaults['sendWelcome']
		);
		if (isset($response['code'])) { // error condition
			$this->errors[] = $response;
			return false;
		}
		return array(
			'email' => isset($response['email']) ? $response['email'] : '(no email set)',
			'userId' => isset($response['euid']) ? $response['euid'] : '(no euid set)',
			'userListId' => isset($response['leid']) ? $response['leid'] : '(no leid set)'	
		);
	}
	
	public function memberInfo($email, $listId = false)
	{
		$this->errors = array();
		if ($listId === false) {
			$listId = Yii::app()->config->mailchimp['list'];
		}				
		return $this->mailchimp->lists->memberInfo($listId, array(array('email'=>$email)));
	}
	
	/**
	 * returns true if the where errors
	 * @return bool
	 */
	public function getHasErrors()
	{
		return count($this->errors) > 0;
	}
	
	public function getDoubleOptIn()
	{
		return Yii::app()->config->mailchimp['doubleOptIn'] != 0;
	}


	public function getMailChimp()
	{
		if ($this->_mailchimp === false) {
			$this->_mailchimp = new Mailchimp($this->apiKey);
			if(empty($this->_mailchimp)) {
				throw new CException(Yii::t('mailchimp', 'The mailchimp connector was not created for api {apiKey}', array('{apiKey}' => $this->apiKey)));
			}
		}
		return $this->_mailchimp;
	}
	
	
	public function getApiKey()
	{
		if ($this->_apiKey === false) {
			$this->_apiKey = Yii::app()->config->mailchimp['apiKey'];
		}
		return $this->_apiKey;
	}
	public function setApiKey($key)
	{
		$this->_apiKey = $key;
	}
	
}