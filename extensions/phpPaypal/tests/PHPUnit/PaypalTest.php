<?php
class Lionite_AuthTest extends UnitTestCase {
	protected $_items = array(
		0 => array(
			'name' => 'Nikon D80 Camera',
			'cost' => '640.95',
			'amount' => 2,
			'number' => 2394
		),
		1 => array(
			'name' => 'HP nc8430 Laptop',
			'cost' => '1100.99',
			'tax' => '40',
			'number' => 485
		)
	);
	protected $_directPaymentData = array(
		"card_type" => "Visa",
		"card_holder" => "John Doe",
		"card_number" => "4580035948599459",
		"expiry_month" =>"05",
		"expiry_year" =>  "2014",
		"cvv" => "545",
		"first_name" => "John",
		"last_name" => "Doe",
		"email" => "johndoe@lionite.com",
		"country" => "US",
		"city" => "New York",
		"state" => "NY",
		"street" => "345 Argyle Rd.",
		"street2" => "",
		"zipcode" => "10010",
		"phone" => "202-349-4567"
	);
	public function setUp() {}
	public function testDeformatNVP() {
		$queryString = '&RETURNURL=http://demo.lionite.com/paypal/confirm&CANCELURL=http://demo.lionite.com/paypal/checkout&L_PAYMENTREQUEST_0_AMT0=310.99&L_PAYMENTREQUEST_0_NAME0=Panasonic+Lumix+DMC-ZS7&L_PAYMENTREQUEST_0_QTY0=1&L_PAYMENTREQUEST_0_NUMBER0=3849&PAYMENTREQUEST_0_AMT=310.99&PAYMENTREQUEST_0_PAYMENTACTION=Sale&PAYMENTREQUEST_0_CURRENCYCODE=USD';
		$paypal = new Lionite_Paypal();
		$result = $paypal -> deformatNVP($queryString);
		$this -> assertIsA($result,'array');
		$this -> assertTrue(count($result) == 9);
	}
	public function testExpressCheckoutToken() {
		$paypal = new Lionite_PaypalStub();
		$url = '/test/url';
		$paypal -> getExpressCheckoutToken(array('return' => $url,'cancel' => $url),$this -> _items);
		$params = $paypal -> getQuery();
		$stringParams = array(
			'RETURNURL','CANCELURL','L_PAYMENTREQUEST_0_NAME0','L_PAYMENTREQUEST_0_NAME1'
		);
		$numericParams = array(
			'PAYMENTREQUEST_0_AMT','L_PAYMENTREQUEST_0_AMT0','L_PAYMENTREQUEST_0_AMT1','L_PAYMENTREQUEST_0_QTY0','L_PAYMENTREQUEST_0_QTY1','L_PAYMENTREQUEST_0_NUMBER0','L_PAYMENTREQUEST_0_NUMBER1'
		);
		
		$this -> _testParams($params,$stringParams,$numericParams);
	}
	
	public function testConfirmCheckoutPayment() {
		$paypal = new Lionite_PaypalStub();
		$result = $paypal -> confirmCheckoutPayment(array('token' => 'ASDC93482ASDC','cost' => 495,'payer_id' => '4594785'));
		$params = $paypal -> getQuery();
		$stringParams = array(
			'TOKEN','PAYERID','IPADDRESS','PAYMENTREQUEST_0_PAYMENTACTION','PAYMENTREQUEST_0_CURRENCYCODE'
		);
		$numericParams = array(
			'PAYMENTREQUEST_0_AMT'
		);
		
		$this -> _testParams($params,$stringParams,$numericParams);
	}
	
	public function testDirectPayment() {
		$paypal = new Lionite_PaypalStub();
		$result = $paypal -> directPayment($this -> _directPaymentData,$this -> _items);
		$params = $paypal -> getQuery();
		$stringParams = array(
			'CREDITCARDTYPE','EXPDATE','FIRSTNAME','LASTNAME','STREET','CITY','STATE','ZIP','COUNTRYCODE','AMT'
		);
		$numericParams = array(
			'ACCT','CVV2'
		);
		$this -> _testParams($params,$stringParams,$numericParams);
		
	}
	
	protected function _testParams($params,$stringParams,$numericParams) {
		foreach($stringParams as $key) {
			if(!array_key_exists($key,$params)) {
				$this -> fail('"' . $key . '" parameter missing');
			}
			$this -> assertIsA($params[$key],'string');
		}
		
		foreach($numericParams as $key) {
			if(!array_key_exists($key,$params)) {
				$this -> fail('"' . $key . '" parameter missing');
			}
			if(!is_numeric($params[$key])) {
				$this -> fail('"' . $params[$key] . '" is not numeric for ' . $key);
			}
		}
	}
}

class Lionite_PaypalStub extends Lionite_Paypal {
	protected $requestQuery;
	public function getQuery() {
		return $this -> requestQuery;
	}
	public function apiCall($methodName,$nvpStr) {
		$this -> requestQuery = $this -> deformatNVP($nvpStr);
		switch ($methodName) {
			case $methodName == 'SetExpressCheckout' :
				return array('ACK' => 'Success','TOKEN' => 'ADSMC345-JFGN43');
				break;
			case $methodName == 'DoExpressCheckoutPayment' :
				return array('ACK' => 'Succcess','PAYMENTINFO_0_TRANSACTIONID' => 'MKFJH483JD94D');
				break;
			case $methodName == 'DoDirectPayment' : 
				return array('ACK' => 'Success','TRANSACTIONID' => 'ASJ395ASK5');
				break;
		}
	}
}