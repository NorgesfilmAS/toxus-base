<?php

/**
 * the interface fo the php-paypal class
 */

class PayPalProvider extends CComponent
{
	/**
	 * the internal class to to the payments
	 * 
	 * @var LionitePaypal 
	 */
	private $_paypal = false;
	
	public function __construct() {
		$c = Yii::app()->config;
		$this->_paypal = new LionitePaypal();
		if (Yii::app()->config->paypal['sandbox']) {			
			$this->_paypal->setup(1, null, array(
				'username' => $c->paypal['sandboxUsername'],
				'password' => $c->paypal['sandboxPassword'],	
				'signature' => $c->paypal['sandboxSignature'],		
			));
		} else {
			$this->_paypal->setup(0, array(
				'username' => $c->paypal['liveUsername'],
				'password' => $c->paypal['livePassword'],	
				'signature' => $c->paypal['liveSignature'],		
			));
		}
	}
}
