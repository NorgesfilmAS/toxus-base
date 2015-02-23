<?php
/**
 * Create a stripe payment
 * 
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
	 * 
	 * @param int $id the id of the item to show,not of the view.
	 */
	public function run()
	{
		$paymentKey = Yii::app()->session['payment'];
		$this->controller->model = Payment::model()->find('slug=:key', array(':key' => $paymentKey));
		
		if (empty($this->controller->model)) {			
			$this->controller->render('error', array('message' => Yii::t('cordtricks', 'The payment could not be found')));
		} else { // we have a payment. So set params for the form
			$isLiveMode = Yii::app()->config->stripe['isLiveMode'];
			
			$this->params['amount'] = $this->controller->model->total_amount * 100;	// must be in cents
			$this->params['currency'] = Yii::app()->config->stripe['currency'];
			$this->params['key'] = $isLiveMode ? Yii::app()->config->stripe['livePublishedKey'] :  Yii::app()->config->stripe['testPublishedKey'];
			
			if (empty($this->params[key])) {				
				$this->controller->render('error', array('message' => Yii::t('cordtricks', 'The payment key could not be found')));
				return;
			}
			$this->controller->render('stripe', $this->params);
		}
	}
}
