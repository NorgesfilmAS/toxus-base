<?php
/**
 * makes the mail system suiteable for the Postmark mail processor
 * 
 * Version 1.0 24 sept 2014
 */

class m140924_082342_mail_tracking extends CDbMigration
{
	public function up()
	{
		$this->addColumn('mail', 'html_message', 'text');
		$this->addColumn('mail', 'from_address', 'string');
		$this->addColumn('mail', 'to_address', 'string');
		$this->addColumn('mail', 'reply_to', 'string');
		$this->addColumn('mail', 'subject', 'string');
		$this->addColumn('mail', 'message_id', 'string');
		$this->addColumn('mail', 'error_code', 'string');
		$this->addColumn('mail', 'error_curl', 'string');
		$this->addColumn('mail', 'bounce_json', 'text');
		$this->addColumn('mail', 'is_bounced', 'tinyint default 0 not null');
		$this->addColumn('mail', 'open_json', 'text');
		$this->addColumn('mail', 'is_inbound', 'tinyint default 0 not null');
		$this->addColumn('mail', 'inbound_json', 'text');
		$this->addColumn('mail', 'spam_status', 'text');
		$this->addColumn('mail', 'spam_score', 'text');
		$this->addColumn('mail', 'spam_tests', 'text');
		$this->addColumn('mail', 'is_reply_text', 'tinyint default 0 not null');
		$this->createIndex('ix_mail_message_id', 'mail', 'message_id');
		$this->createIndex('ix_mail_is_bounced', 'mail', 'is_bounced');
		$this->createIndex('ix_mail_to_address', 'mail', 'to_address');
	}

	public function down()
	{
		$this->dropIndex('ix_mail_message_id', 'mail');
		$this->dropIndex('ix_mail_is_bounced', 'mail');
		$this->dropIndex('ix_mail_to_address', 'mail');
		$this->dropColumn('mail', 'is_reply_text');
		$this->dropColumn('mail', 'spam_status');
		$this->dropColumn('mail', 'spam_score');
		$this->dropColumn('mail', 'spam_tests');
		$this->dropColumn('mail', 'is_inbound');
		$this->dropColumn('mail', 'inbound_json');
		$this->dropColumn('mail', 'open_json');
		$this->dropColumn('mail', 'is_bounced');
		$this->dropColumn('mail', 'bounce_json');
		$this->dropColumn('mail', 'error_curl');
		$this->dropColumn('mail', 'error_code');
		$this->dropColumn('mail', 'to_address');
		$this->dropColumn('mail', 'subject');
		$this->dropColumn('mail', 'message_id');
		$this->dropColumn('mail', 'reply_to');
		$this->dropColumn('mail', 'from_address');
		$this->dropColumn('mail', 'html_message');
		return true;
	}

	
}
