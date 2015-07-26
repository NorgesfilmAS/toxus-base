<?php
/**
 * standard payment provider:
 * 
 * extension:
 *  $this->bankList : array a list of bank know to the syste,
 *	$this->bankId: integer the id of the bank to use
 * 
 *  version 1.0 JvK 2013.11.12
 */


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
	public function getBankList() 
	{
		$result = '';
		$err = $this->sisow->DirectoryRequest($result, false, $this->isTestMode);
		if ($err == 0)
			return $result;
		throw new ESisowException('Unable to load bank information ('.$err.')');		
	}
	
	protected function startPayment()
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
	
}
