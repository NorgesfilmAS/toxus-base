<?php

Yii::import('toxus.extensions.paymentProvider.sisow.*');			

class SisowIdeal extends PaymentProvider
{
	public $name = 'Sisow Ideal';

	private $_sisow = null;
	
	public function getSisow()
	{
		if (empty($this->_sisow)) {
			$this->_sisow = new SisowApi($this->key('sisow.merchantId'), $this->key('sisow.merchantKey'));
			if (empty($this->_sisow)) { 
				throw new ESisowException('Unable to create payment definition');
			}	
		}
		return $this->_sisow;
	}
	
	
	/**
	 * list the banks to use
	 */
	public function getItemsList() 
	{
		$result = '';
		$err = $this->sisow->DirectoryRequest($result, false, $this->isTestMode);
		if ($err == 0)
			return $result;
		throw new ESisowException('Unable to load bank information ('.$err.')');		
	}
	
	public function startPayment()
	{
		$this->_errorCode = '';
		$this->_errorMessage = null;
		
		$this->sisow->amount = $this->model->total_amount;
		$this->sisow->returnUrl =  $this->successUrl;
		$this->sisow->cancelUrl = $this->cancelUrl;
		$this->sisow->notifyUrl = $this->notifyUrl;
 		$this->sisow->payment = '';															// '' = ideal, ebill digi accept, overboeking
		$this->sisow->issuerId = $issuerId;											// the selected bank
		$this->sisow->description = $this->model->description;	// description on the invoice
		$this->sisow->purchaseId = '';													// betalingskenmerk/
		$this->sisow->testmode = $this->isTestMode;
		
		$exitCode = $this->sisow->TransactionRequest();
		if ($exitCode < 0) {
			$this->addError('Sisow exitcode: '.$exitCode);
			return false;
		}
		return $this->sisow->issuerUrl;		
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
	public function transaction($amount, $returnUrl, $cancelUrl = '', $notifyUrl = '', $paymentType = '', $issuerId='', $desciption='', $purchaseId = '')					
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
	
	
}
