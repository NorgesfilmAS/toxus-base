<?php
/**
 * Create a stripe payment
 * 
 * ======= VERSION IS UNTESTED AND NEVER RUNNED !!!!! ====================
 * 
 * version 0.01 JVK 2015.02.24
 */
yii::import('toxus.actions.BaseAction');

class StripeAction extends BaseAction {
	
	/**
	 * the page shown for loading
	 * 
	 * @var string
	 */
	public $view = 'stripe';
	/**
	 * default all columns
	 * @var string
	 */
	public $pageLayout = 'full';
	/**
	 * url to call by strip when returned. Must be an absolute url
	 * Should be this url will param sold=1
	 * @var string
	 */
	public $paidUrl = false;
	
	/**
	 * 
	 * @param int $id the id of the item to show,not of the view.
	 */
	public function run($sold=0)
	{
		$paymentKey = Yii::app()->session['payment'];
		$this->controller->model = Payment::model()->find('slug=:key', array(':key' => $paymentKey));
		
		if (empty($this->controller->model)) {			
			$this->controller->render('error', array('message' => Yii::t('cordtricks', 'The payment could not be found')));
		} else { // we have a payment. So set params for the form
			if ($sold == 0) {
				if (!$this->paidUrl) {
					throw new CDbException('There is no paidUrl set');
				}
				$isLiveMode = Yii::app()->config->stripe['isLiveMode'];

				$this->params['url'] = $this->paidUrl;
				$this->params['amount'] = $this->controller->model->total_amount * 100;	// must be in cents
				$this->params['email'] = $this->controller->model->userProfile->email;
				$this->params['currency'] = Yii::app()->config->stripe['currency'];
				$this->params['key'] = Yii::app()->config->stripe[$isLiveMode ? 'livePublishedKey' : 'testPublishedKey'] ;
				if (empty($this->params[key])) {				
					$this->controller->render('error', array('message' => Yii::t('cordtricks', 'The payment key could not be found')));
					return;
				}
				$this->controller->render('stripe', $this->params);
			}
		}
	}
}
