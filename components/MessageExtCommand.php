<?php
/**
 * fixes a bug in Yii that there are false keys in the message queue
 * 
 * version 1.0 JvK 2014.08.01
 */

Yii::import('system.cli.commands.MessageCommand');
class MessageExtCommand extends MessageCommand
{
	protected function generateMessageFile($messages,$fileName,$overwrite,$removeOld,$sort,$fileHeader)
	{
		foreach ($messages as $key => $message) {
			if ($message == false) {
				unset($messages[$key]);
			}
		}
		return parent::generateMessageFile($messages, $fileName, $overwrite, $removeOld, $sort, $fileHeader);
	}
}