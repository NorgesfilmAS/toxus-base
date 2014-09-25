<?php

Yii::import('toxus.models._base.BaseMail');

class MailModel extends BaseMail
{
	private $_inbound = false; // false: not loaded, 0 : no inbound avail, array: inbound information
	/**
	 * set to true to reprocess the inbound definition
	 * @var bool
	 */
	protected $_inboundProcess = false;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * inbound is only process on isNewRecord or by setting $this->_inboundProcess = true;
	 * @return
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->creation_date = new CDbExpression('NOW()');
		}
		if ($this->isNewRecord || $this->_inboundProcess) {			
			$this->is_inbound = !empty($this->inbound_json);
			if ($this->is_inbound) {
				if (isset($this->inbound['strippedTextReply'])) {
					$this->message = $this->inbound['strippedTextReply'];
					$this->is_reply_text = 1;
				}	else {
					$this->message = $this->inbound['textBody'];					
				}
				if (isset($this->inbound['htmlBody'])) {
					$this->html_message = $this->inbound['htmlBody'];
				}
				if (isset($this->inbound['headers'])) {
					if (isset($this->inbound['X-Spam-Status'])) {
						$this->spam_status = $this->inbound['X-Spam-Status'];
					}
					if (isset($this->inbound['X-Spam-Score'])) {
						$this->spam_score = $this->inbound['X-Spam-Score'];
					}
					if (isset($this->inbound['X-Spam-Tests'])) {
						$this->spam_tests = $this->inbound['X-Spam-Tests'];
					}
				}
				if (isset($this->inbound['from'])) {
					$this->from_address = $this->inbound['from'];
				}
				if (isset($this->inbound['to'])) {
					$this->to_address = $this->inbound['to'];
				}
				if (isset($this->inbound['subject'])) {
					$this->subject = $this->inbound['subject'];
				}
				if (isset($this->inbound['messageId'])) {
					$this->message_id = $this->inbound['messageId'];
				}
				if (isset($this->inbound['replyTo'])) {
					$this->reply_to = $this->inbound['replyTo'];
				}				
			}					
		}
		$this->is_bounced = empty($this->bounce_json) ? 0 : 1;
		
		return parent::beforeSave();
	}
	
	/**
	 * return the bounce information if any. False if not bounce info
	 *	- type : 
	 *  - typeCode :
	 *	- name : the text version of the type
	 *  - messageId : the same as $this->message_id
	 *  - description :
	 *  - details :
	 *  - email : the address that bounced
	 *  - bounceAt : the string date
	 *  - dumpAvailable : bool 
	 *  - inActive : bool the email address has been deactivated by this bounce
	 *  - canActive : bool can activate again. Only if not Spam
	 *  - subject: same as $this->subject 
	 * 
	 * @return boolean|array
	 */
	public function getBounce()
	{
		if (!empty($this->bounce_json)) {
			return Util::properArrayKeys(CJSON::decode($this->bounce_json));
		} else {
			return false;
		}
	}
	
	public function getInbound()
	{
		if ($this->_inbound === false) {
			if (empty($this->inbound_json)) {
				$this->_inbound = 0; 				
			} else {
			  $this->_inbound = Util::properArrayKeys(CJSON::decode($this->inbound_json));
			}
		}
		if ($this->_inbound === 0) {
			return false;
		}
		return $this->_inbound;
	}
}