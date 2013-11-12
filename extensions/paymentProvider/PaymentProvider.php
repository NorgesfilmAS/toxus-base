<?php
/**
 * basis for a payment provider
 * How to:
 *  - create a fully qualified PaymentModel record ($pay = new PaymentModel() ) 
 *    and save it
 *  - for testing set isTestMode (default is true: Yii::app()->params['paymentprovider.test] )
 *  - $provider = new [Subclass of PaymentProvider]()  (can be SisowIdeal, PayPalCreditCard)
 *  - set any extra information ($provider->bankId)
 *  - $pay->transaction($pay->slug)
 *  -
 */
class PaymentProvider extends CComponent
{
	// used in Yii::app()->params['paymentUrl'] to set the absolute url for this system
	const PAYMENT_URL = 'paymentUrl';
	
	/**
	 * name shown to the user
	 * 
	 * @var string 
	 */
	public $name = 'payment provider';
	
	/**
	 * true: run in the sandbox
	 * @var boolean 
	 */
	public $isTestMode = true;
	
	/**
	 * the model holding the transaction. Is set on $this->transaction
	 * 
	 * @var PaymentModel
	 */
	public $paymentModel = null;
	public $paymentModelClass = 'PaymentModel'; // class for the PaymentModel
	
	/**
	 * the errors during the processing
	 * 
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * the url (http://www.example.com/controller) that will be used for the reply
	 * of the payment server (http://www.example.com/controller/success/aDsfGkjasldfkjalsdkfjlasdf, etc)
	 * 
	 * @var string
	 */
	public $absoluteUrl = false;
	
	/**
	 * TEMP: the array loaded from Yii::app()->params['payment']
	 * with the keys information
	 *  
	 * @var array
	 */
	protected $_clientConfig = false;
	
	public function init()
	{
		if (isset(Yii::app()->params[self::PAYMENT_URL])) {
			$this->absoluteUrl = Yii::app()->params[self::PAYMENT_URL];
		}
	}
	
	public function addError($msg)
	{
		$this->errors[] = $msg;
	}
	public function getErrorMessage()
	{
		return implode("\n", $this->errors);
	}
	/**
	 * loads unique keys from the customer database
	 * TEMP: uses fix debug strings
	 * 
	 * @param string $name
	 * @param string $default
	 */
	public function getKey($name, $default = null)
	{
		if (!is_array($this->_clientConfig )) {
			$this->_clientConfig = Yi::app()->params['payments'];
		}
		if (!isset($this->_clientConfig[$name])) {
			Yii::app()->pageLog->addMsg('Payment: Key ('.$name.') not found');
			return $default;
		} else {
			return $this->_clientConfig[$name];
		}
	}
	
	/**
	 * 
	 * @return array of items to select from or false if there is no list
	 */
	public function getItemsList()
	{
		return false;
	}
	
	/**
	 * called when transaction succeeded
	 */
	public function success()
	{
	}
	protected function getSuccessUrl()
	{
		return $this->absoluteUrl.'/success/'.$this->model->slug;
	}
	/**
	 * called when transaction is canceld
	 */
	public function cancel()
	{
	}
	protected function getCancelUrl()
	{
		return $this->absoluteUrl.'/canceled/'.$this->model->slug;
	}
	/**
	 * Called when the Payment provider as to tell something
	 */
	public function notify()
	{		
	}
	public function getNotifyUrl()
	{
		return $this->absoluteUrl.'/notify/'.$this->model->slug;
	}
	
	/**
	 * Runs a transaction through the system
	 * 
	 * @param string $slug the slug in the PaymentModel record
	 */
	public function transaction($slug)
	{
		$model = $this->paymentModelClass;
		$this->paymentModel = $model::model()->find('slug=:slug', array(':slug' => $slug));
		if (!$this->paymentModel) {
			throw new CDbException('Payment not found');
		}
		// check the status is 0, because we can NOT redo an action
		if (!($this->status_id == PaymentModel::PAYMENT_NONE || $this->status_id == PaymentModel::PAYMENT_PENDING)) {
			throw new CDbException('Payment is in use');
		}
		if (!$this->absoluteUrl) {
			throw new CException('The absoluteUrl is not set');
		}
		// we have to change the Payment so we know what kind of transaction it was
		$this->paymentModel->paymentProvider = get_class($this);
		$this->paymentModel->status_id = PaymentModel::PAYMENT_STARTING;
		$this->paymentModel->save();
		
		try {
			if (!($url = $this->startPayment())) {		
				$this->paymentModel->status_id = PaymentModel::PAYMENT_ERROR;
				$this->paymentModel->status_text .= get_class($this). '.paymentModel returned an error:'.$this->getErrorMessage()."\n";
			} else {
				$this->paymentModel->status_id = PaymentModel::PAYMENT_TRANSACTION;				
				if ($url !== '') { // redirect to the url given
					Yii::app()->getRequest()->redirect($url, true, 200);
				}
			}	
			$this->paymentModel->save();
		} catch (Exception $e) {
			$this->paymentModel->status_id = PaymentModel::PAYMENT_ERROR;
			$this->paymentModel->status_text .= 'Error staring transaction: '.$e->getMessage()."\n";
			$this->paymentModel->save();
			throw $e;
		}	
		return true; // all went well, but why did we get here?
	}
	/**
	 * 
	 * staring the payment routines.
	 * should return true if all went well, otherwise $this->getErrorMessage ($this->addError) will be stored
	 * 
	 * $this->model : the payment to execute (PaymentModel)
	 * 
	 * @return string the url to open, false on error, or if nothing to do ''
	 */
	public function startPayment()
	{
		throw new CHttpException(500, 'There is no startPayment defined for the '.get_class($this));
	}
}