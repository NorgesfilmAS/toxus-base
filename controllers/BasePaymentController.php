<?php
/**
 * payments in the system:
 * 
 * user presses the buy button:
 *  index:		show screen what they are buying, with the price and possible coupon 
 *						before starting the payment, the Payment record should made available. 
 *
 *						if $_POST['Payment'] then coupon is checked an information is redisplayed
 *	
 *	confirm:	the user is shown a info about product. User must select payment method
 *						(credit card, IDeal, etc)
 *						$_POST['Payment'] update the information and show extra information (bank, etc)
 * 
 *  buy:			redirect to/from payment provider: notification what did happen.
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
	
	public $indexForm = false;
	/**
	 * the startup of the payment system
	 * 
	 * @param $id string the slug of the payment record
	 * @mode what to execute
	 */
	public function actionIndex($id=null, $mode=null)
	{
		$this->loadModel($id);
		if ($_POST[$this->modelName]) {
			if (isset($_POST[$this->modelName]['coupon'])) {
				$this->model->coupon = $_POST[$this->modelName]['coupon'];	
			}
		}
		$params = array();
		if ($this->indexForm) {
			$params['form'] = $this->loadForm($this->indexForm);
		}

		$this->render($this->indexView, $params);
	}
	
	protected function loadModel($slug)
	{
		$modelName = $this->modelName;
		if ($slug == null) 
			throw new CDbException(Yii::t('app', 'No payment reference is active'));		
		$this->model = $modelName::model()->find('slug=:slug', array(':slug' => $slug));
		if ($model == null) 
			throw new CDbException(Yii::t('Payment reference not found'));
		return true;
	}
}