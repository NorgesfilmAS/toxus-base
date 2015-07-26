<?php
/**
 * payments in the system:
 * 
 * user presses the buy button:
 *  index:		show screen what they are buying, with the price and possible coupon 
 *						before starting the payment, the Payment record should made available.  
 *						if $_POST['Payment'] then coupon is checked an information is redisplayed
 *	
 *	provider:	Show the payment provider options for selecting type of payment, bank etc
 * 
 * 
 *  buy:			redirect to/from payment provider: notification what did happen.
 * 
 * 
 * The states called by the payment profider
 *  success
 *  cancel
 *  notify
 *
 * 	
 *  in the main.php the line:
 *    payment/<id:.*?>/<mode:/*?>
 * 
 */


class BasePaymentController extends Controller
{
	/**
	 * the model onwhich the payments are build
	 * @var string
	 */
	public $modelName = 'Payment';
	
	/**
	 * the view to show on the index page
	 * @var string
	 */
	public $indexView = 'index';
	/**
	 * the view to open when selecting the provider
	 * @var string the name of the view
	 */
	public $providerView = 'provider';
	
	public $indexForm = false;
	/**
	 * the startup of the payment system
	 * shows main, with body of payment/index.
	 * embedded is payment/totals with form for coupons
	 * 
	 * 
	 * @param $id string the slug of the payment record
	 * @mode what to execute
	 */
	public function actionIndex($id=null)
	{
		$this->loadModel($id);
		if ($_POST[$this->modelName]) {
			if (isset($_POST[$this->modelName]['coupon'])) {
				$this->model->couponCode = $_POST[$this->modelName]['coupon'];	
			}
		}
		$params = array(				
			'form' => $this->loadForm('couponFields')
		);
		$this->render($this->indexView, $params);
	}
	
	public function loadModel($key, $modelClass)
	{
		$modelName = $this->modelName;
		if ($key == null) 
			throw new CDbException(Yii::t('base', 'No payment reference is active'));		
		$this->model = $modelName::model()->find('slug=:slug', array(':slug' => $slug));
		if ($this->model == null) 
			throw new CDbException(Yii::t('base','payment reference not found'));
		$this->model->recalculate();
		return true;
	}
	
	public function actionProvider($id=null)
	{
		$this->loadModel($id);
		if ($_POST[$this->modelName]) {
			if (isset($_POST[$this->modelName]['coupon'])) {
				$this->model->couponCode = $_POST[$this->modelName]['coupon'];	
			}
		}
		$params = array();
		$params['form'] = $this->loadForm('providerFields');
		
		$this->render($this->providerView, $params);
	}
	
	protected function logState($message = null)
	{
		$log = new LoggingModel();
		$log->message = $message();
		$log->save();
	}
	
	public function actionSuccess($id=null)
	{
		$this->logState('PaymentController.success');
		try {
			$this->loadModel($id);
			$this->model->state = PaymentModel::PAYMENT_SUCCESS;
			$this->model->save();
			$class = $this->model->payment_provider;
			$provider = new $class();
			$provider->paymentModel = $this->model;
			$class->success();
		} catch (Exception $e) {
			$this->logState('PaymentController.success: error: '.$e->getMessage());
			throw $e;
		}	
		$this->end(200);
	}
	public function actionCancel($id=null)
	{
		$this->logState('PaymentController.cancel');
		try {
			$this->loadModel($id);
			$this->model->state = PaymentModel::PAYMENT_CANCEL;
			$class = $this->model->payment_provider;
			$this->model->save();			
			$provider = new $class();
			$provider->paymentModel = $this->model;
			$class->canceled();
		} catch (Exception $e) {
			$this->logState('PaymentController.canceled error: '.$e->getMessage());
			throw $e;
		}	
		$this->end(200);
	}
	public function actionNotify($id=null)
	{
		$this->logState('PaymentController.notify');
		try {
			$this->loadModel($id);
			$class = $this->model->payment_provider;
			$provider = new $class();
			$provider->paymentModel = $this->model;
			$class->notify();
		} catch (Exception $e) {
			$this->logState('PaymentController.notify error: '.$e->getMessage());
			throw $e;
		}	
		$this->end(200);
	}
}