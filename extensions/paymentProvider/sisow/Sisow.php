<?php
/**
 * standard definition of online payments through sisow
 * 
 */

class Sisow extends CComponent
{
	private $_errorCode = 0;
	private $_errorMessage = null;
	
	public $merchantId = '2537477258';
	public $merchantKey = '3c951eed245c1864f4af6608d4950381b7f77370';
	public $testMode = true;
	public $daysTillReminder = 1;
	
	private $_sisow = null;

	public function init()
	{
	}
	
	public function getSisow()
	{
		if (empty($this->_sisow)) {
			Yii::import('ext.payment.sisow.*');			
			$this->_sisow = new SisowApi($this->merchantId, $this->merchantKey);
			if (empty($this->_sisow)) { 
				throw new ESisowException('Unable to create payment definition');
			}	
		}
		return $this->_sisow;
	}

	
	public function paymentTypeOptions()
	{
		return array(
			'' => Yii::t('base', 'pay-ideal'),
		//	'overboeking' => Yii::t('app', 'pay-ebanking'),	
		);
	}
	public function bankOptions()
	{
		$err = $this->sisow->DirectoryRequest($result, false, $this->testMode);
		if ($err == 0)
			return $result;
		throw new ESisowException('Unable to load bank information ('.$err.')');
	}
	
	/**
	 * Does the transaction with Sisow
	 * 
 	 * @param string $amount the amount to be paid
	 * @param string $returnUrl the url called when all went well
	 * @param string $cancelUrl the url called when canceld
	 * @param string $paymentType the type of transaction ('' = ideal, ebill digi accept, overboeking
	 * @param string $issuerId The id of the bank. If not avail, sisow will decide, if paymentType = overboeking => email
	 * @param string $description the description for the payment
	 * @param string $purchaseId (betalingskenmerk max 16 char)
	 * 
	 * @return string the url to redirect to or false if an error (errorCode, errorMessage) happend
	 */
	public function transaction($amount, $returnUrl, $cancelUrl = '', $notifyUrl = '', $paymentType = '', $issuerId, $desciption='', $purchaseId = '')					
	{
		$this->_errorCode = '';
		$this->_errorMessage = null;
		
		$this->sisow->amount = $amount;
		$this->sisow->returnUrl = $returnUrl;
		$this->sisow->cancelUrl = $cancelUrl;
		$this->sisow->notifyUrl = $notifyUrl;
 		$this->sisow->payment = $paymentType;
		$this->sisow->issuerId = $issuerId;
		$this->sisow->description = $desciption;
		$this->sisow->purchaseId = $purchaseId;
		$this->sisow->testmode = $this->testMode;
		
		$exitCode = $this->sisow->TransactionRequest();
		if ($exitCode < 0) {
			return false;
		}
		return $this->sisow->issuerUrl;
	}

	public function overboeking($amount, $returnUrl, $email, $days, $cancelUrl = '', $notifyUrl = '', $paymentType = '', $issuerId, $desciption='', $purchaseId = '')					
	{
		$this->_errorCode = '';
		$this->_errorMessage = null;
		
		$this->sisow->amount = $amount;
		$this->sisow->email = $email;
		$this->sisow->days = $days;
		$this->sisow->returnUrl = $returnUrl;
		$this->sisow->cancelUrl = $cancelUrl;
		$this->sisow->notifyUrl = $notifyUrl;
 		$this->sisow->payment = $paymentType;
		$this->sisow->issuerId = $issuerId;
		$this->sisow->description = $desciption;
		$this->sisow->purchaseId = $purchaseId;
		$this->sisow->testmode = $this->testMode;
		
		$exitCode = $this->sisow->Overboeking();
		if ($exitCode < 0) {
			return false;
		}
		return $this->sisow->issuerUrl; // is empty
	}
	
	
	private function listTransaction()
	{
		$ret = 'trxId: '.$this->sisow->trxId."\n";
//		$ret .= 'merchantId: '.$this->sisow->merchantId."\n";
//		$ret .= 'merchantKey: '.$this->sisow->merchantKey."\n";
		$ret .= 'status: '.$this->sisow->status."\n";
		$ret .= 'timeStamp: '.$this->sisow->timeStamp."\n";
		$ret .= 'amount: '.$this->sisow->amount."\n";
		$ret .= 'consumerAccount: '.  $this->sisow->consumeraccount."\n";
		$ret .= 'consumerName: '.$this->sisow->consumername."\n";
		$ret .= 'consumerCity:'.$this->sisow->consumercity."\n";
		$ret .= 'purchaseId: '.$this->sisow->purchaseid."\n";
		$ret .= 'description: '.$this->sisow->description."\n";
		$ret .= 'entranceCode:'.$this->sisow->entrancecode."\n";
		return $ret;
	}
	
	public function status($transactionId) 
	{
		$err = $this->sisow->StatusRequest($transactionId);
		if ($err == 0) {
			return $this->listTransaction();
		} else {
			return $err;
		}
	}
	
	public function getErrorCode()
	{
		return $this->sisow->errorCode;
	}
	public function getErrorMessage()
	{
		return $this->sisow->errorMessage.' (errorcode: {code})';
	}
	public function getTransaction_id()
	{
		return $this->sisow->trxId;
	}
	
}


?>
