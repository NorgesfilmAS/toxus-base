<?php

Yii::import('toxus.extension.php-paypal.library.Lionite.Paypal');
class LionitePaypal extends Lionite_Paypal 
{
	/**
	 * init the lionite class
	 * 
	 * the options array can use:
			'username'
			'password'
			'signature'
	 * 
	 * 
	 * @param bool sandbox true|false
	 * @param array $liveOptions
	 * @param array $sandboxOptions
	 */
	public function setup($sandbox = null, $liveOptions = null, $sandboxOptions=null) 
	{
		if (is_array($liveOptions)) {
			foreach ($liveOptions as $key => $option) {
				if (!isset($this->_settings['live'][$key])) {
					throw new CException('The key: '.$key.' does not exist');
				}
				$this->_settings['live'][$key] = $option;
			}
		}
		if (is_array($sandboxOptions)) {
			foreach ($sandboxOptions as $key => $option) {
				if (!isset($this->_settings['live'][$key])) {
					throw new CException('The key: '.$key.' does not exist');
				}				
				$this->_settings['sandbox'][$key] = $option;
			}
		}
		if ($sandbox !== null) {
			self::sandbox($sandbox);
		}		
	}
}