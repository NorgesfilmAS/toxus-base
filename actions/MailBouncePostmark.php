<?php
/**
 * process the bounce of a email returned by Postmark (www.postmarkapp.com)
 * 
 * version 1.0 JvK
 * 
 * doc:  
 *   http://developer.postmarkapp.com/developer-bounce-webhook.html
 * 
 * to test:
  curl <your-url> \
		-X POST \
		-H "Content-Type: application/json" \
		-d '{ "ID": 42, "Type": "HardBounce", "TypeCode": 1, "Name": "Hard bounce", "Tag": "Test", "MessageID": "883953f4-6105-42a2-a16a-77a8eac79483", "Description": "The server was unable to deliver your message (ex: unknown user, mailbox not found).", "Details": "Test bounce details", "Email": "john@example.com", "BouncedAt": "2014-08-01T13:28:10.2735393-04:00", "DumpAvailable": true, "Inactive": true, "CanActivate": true, "Subject": "Test subject" }'
 * 
 */

Yii::import('toxus.actions.BaseAction');
class MailBouncePostmark extends BaseAction
{
	/**
	 *
	 * @var string the path used for the logging of the events
	 */
	public $logPath = 'toxus.mail';
	/**
	 * the model class used to find the mail message store
	 * @var string 
	 */
	public $mailModel = 'MailModel';
	
	/**
	 * Fields: 
	 {
  "ID": 42,
  "Type": "HardBounce",
  "TypeCode": 1,
  "Name": "Hard bounce",
  "Tag": "Test",
  "MessageID": "883953f4-6105-42a2-a16a-77a8eac79483",
  "Description": "The server was unable to deliver your message (ex: unknown user, mailbox not found).",
  "Details": "Test bounce details",
  "Email": "john@example.com",
  "BouncedAt": "2014-08-01T13:28:10.2735393-04:00",
  "DumpAvailable": true,
  "Inactive": true,
  "CanActivate": true,
  "Subject": "Test subject"
} 
	 * 
	 * @throws CHttpException
	 */
	
	public function run($key=false)
	{
		$bounceKey = Yii::app()->config->mail['bounceKey'];
		if ( ($key==false && !empty($bounceKey)) || $key != $bounceKey) {
			Yii::log('The key is invalid', CLogger::LEVEL_ERROR, $this->logPath);
			throw new CHttpException(403, 'Access denied');
		}
		
		if(!Yii::app()->request->isPostRequest) {
			Yii::log('The MailBouncePostmark should be a POST statement. Temporary using fake information', CLogger::LEVEL_ERROR, $this->logPath);
			$json = '{ "ID": 1, "Type": "FakeBounce", "TypeCode": 1, "Name": "Hard bounce", "Tag": "Test", "MessageID": "883953f4-6105-42a2-a16a-77a8eac79483", "Description": "The server was unable to deliver your message (ex: unknown user, mailbox not found).", "Details": "Test bounce details", "Email": "john@example.com", "BouncedAt": "2014-08-01T13:28:10.2735393-04:00", "DumpAvailable": true, "Inactive": true, "CanActivate": true, "Subject": "Test subject" }';
			//throw new CHttpException(Yii::t('base', 'Bounce can only be a post request'));
		} else {
			$json = file_get_contents('php://input');
		}	
		$fields = Util::properArrayKeys(CJSON::decode($json));
		
		$class = $this->mailModel;
		$messageId = '';
		$mail = false;
		if (isset($fields['messageID'])) {
			$messageId = $fields['messageID'];			
			$mail = $class::model()->find('message_id=:messageId', array(':messageId' => $messageId));
			if (!empty($mail)) {
				Yii::log('Found existing message '.$mail->id, CLogger::LEVEL_INFO, $this->logPath);
			} else {
				Yii::log('MessageId '.$messageId.' does not exist', CLogger::LEVEL_WARNING, $this->logPath);
			}
		} else {
			Yii::log('There is not messageID', CLogger::LEVEL_WARNING, $this->logPath);
		}
		if (empty($mail)) {
			$mail = new $class;
			$mail->message_id = $messageId;
		}
		$mail->bounce_json = $json;
		if (empty($mail->from_address)) {
			$mail->from_address = isset($fields['email']) ? $fields['email'] : null;
		}
		if (!$mail->save()) {
			Yii::log('The mail message could not be save on bounce. ('.Util::errorToString($mail->errors), CLogger::LEVEL_ERROR, $this->logPath);
			echo Util::errorToString($mail->errors);
			return;
		}
		echo 'ok';
		return;
	}
}