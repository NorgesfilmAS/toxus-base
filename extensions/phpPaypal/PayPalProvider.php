<?php

/**
 * the interface fo the php-paypal class
 * 
 * per transaction:
 * 'shipping'		: default(0)
 * 'handling'		: default(0)
 * 'return'		: default: provider/paypalConfirm
 * 'cancel'		: default: provider/paypalCancel
 * 'allow_note': default(0)
 * 'useraction': default('commit')
 * 'currency'  : default: EURO
 * 'custom'    : (not added)
 *
 * 
 */

Yii::import('toxus.extensions.phpPaypal.LionitePaypal');
class PayPalProvider extends CComponent
{
	private $_confirmUrl = false;
	private $_cancelUrl = false;
	private $_payment = false;
	private $_currency = 'EUR';
	
	/**
	 * the internal class to to the payments
	 * 
	 * @var LionitePaypal 
	 */
	private $_paypal = false;
	
	public function __construct() {
		$pp = Yii::app()->config->paypal;
		$this->_paypal = new LionitePaypal();
		if (isset($pp['logging']) && $pp['logging']) {
			$this->_paypal->onLog = array($this, 'logStep');
		}	
		$this->sandbox = Yii::app()->config->paypal['sandbox'];
		$this->init();
	}
	
	public function init()
	{
	}
	
	public function setSandbox($value)  {
		$c = Yii::app()->config;
		if ($value) {			
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
	public function getSandbox()
	{
		return LionitePaypal::sandbox;
	}
			
	/**
	 * 
	 * @return array of checkout Details
	 * @throws CException
	 */
	public function checkoutDetails($token=null, $params=false)
	{
		if ($token == null) {
			if (!isset($_GET['token'])) {
				throw new CException('There is no token and _GET[token] does not exist');
			}
			$token = $_GET['token'];
		}
		return $this->_paypal->getCheckoutDetails($token, $params);
	}
	
	/**
	 * Returns the id of the payment during confirmation
	 * 
	 * @return string|bool the id of the the payment confirmed
	 */
	public function confirmId($token=false, $items=array())
	{
		return $this->_paypal->confirmCheckoutPayment($token, $items);
	}
	
	/**
	 * the array of errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_paypal->getErrors();
	}
	
	/**
	 * returns the url to redirect to on an express checkout,
	 * if false the getError will return the errors
	 * 
	 * @return string|boolean
	 */
	public function getRedirectUrl()
	{
		$result = $this->_paypal->getCheckoutUrl( 
						$this->paymentOptions, 
						$this->itemOptions);
		if (is_string($result)) {
			$this->_payment->payment_provider = 'paypal';
			$this->_payment->transaction_id = $this->_paypal->token;
			$this->logStep(array('correlation_id' => $this->_paypal->correlationId));
			return $result;
		}
		return false;
	}				

	public function getCancelUrl()
	{
		if ($this->_cancelUrl === false) {
			return Yii::app()->controller->createAbsoluteUrl('payment/paypalCancel');
		}
		return $this->_cancelUrl;
	}
	public function getConfirmUrl()
	{
		if ($this->_confirmUrl === false) {
			return Yii::app()->controller->createAbsoluteUrl('payment/paypalConfirm');
		}
		return $this->_confirmUrl;
	}
	public function getCurrency()
	{
		if ($this->_currency === false) {
			$p = Yii::app()->config->paypal;
			if (isset($p['currency'])) {
				return $p['currency'];
			}
			return 'EUR';
		}
		return $this->_currency;
	}
	public function setCurrency($value) {
		if (!in_array($value, array('EUR','GBP','USD'))) {
			throw new CException('Unknown currency: '.$value);
		} 
		$this->_currency = $value;
	}
	
	public function setPayment($value)
	{
		$this->_payment = $value;
	}
	public function getPayment()
	{
		return $this->_payment;
	}
	
	public function getItemOptions()
	{
		$items = array( array(
			'cost' => $this->_payment->total_amount_ex_vat,
			'amount' => 1,
			'name' => $this->_payment->caption,
			'tax' => $this->_payment->vat_amount					
		));

		return $items;		
	}
	
	public function getPaymentOptions()
	{
		return array(
			'return' => $this->confirmUrl,
			'cancel' => $this->cancelUrl,
			'currency' => $this->_payment->currency,	
		);
	}
	
	public function logStep($options)
	{
		if ($this->payment) {
			$this->payment->logStep($options);
		}
	}
}
