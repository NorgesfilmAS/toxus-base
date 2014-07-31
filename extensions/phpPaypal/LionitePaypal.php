<?php

require_once(YiiBase::getPathOfAlias('toxus.extensions.phpPaypal.library.Lionite.Paypal').'.php');
class LionitePaypal extends Lionite_Paypal 
{
	public $onLog = false;	// function to call when logging
	
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
			$this->log('setup.live', $liveOptions);
			foreach ($liveOptions as $key => $option) {
				if (!isset($this->_settings['live'][$key])) {
					throw new CException('The key: '.$key.' does not exist');
				}
				$this->_settings['live'][$key] = $option;
			}
		}
		if (is_array($sandboxOptions)) {
			$this->log('setup.sandbox', $sandboxOptions);
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
	
	/**
	 * Log our calls the api
	 */
	public function apiCall($methodName, $nvpStr) {
		$this->log('apiCall.'.$methodName, $nvpStr);
		return parent::apiCall($methodName, $nvpStr);
	}
	
	public function log($location, $params=array())
	{
		if ($this->onLog !== false) {
			if (!is_array($params)) {
				$p = array(
						'location' => $location,
						'params' => $params);
			} else {
				$p = array_merge(
						array('location' => $location), 
						$params);
			}	
			call_user_func($this->onLog, $p);
		}
	}
}