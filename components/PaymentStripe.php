<?php
/**
 * payment through stripe. See: www.stripe.com
 * 
 * version 1.0 jvk 2015.02.23
 * 
 */

require_once(dirname(__FILE__).'/../extensions/stripe/init.php');

class PaymentStripe extends CComponent {
	/**
	 \Stripe\Stripe::setApiKey("sk_test_BQokikJOvBiI2HlWgH4olfQ2");

	// Get the credit card details submitted by the form
	$token = $_POST['stripeToken'];

	// Create the charge on Stripe's servers - this will charge the user's card
	try {
	$charge = \Stripe\Charge::create(array(
		"amount" => 1000, // amount in cents, again
		"currency" => "usd",
		"source" => $token,
		"description" => "payinguser@example.com")
	);
	} catch(\Stripe\Error\Card $e) {
		// The card has been declined
	}
	 */	
	public function createPayment($token, $amount) {
		$isLiveMode = Yii::app()->config->stripe['isLiveMode'];
		$privateKey = Yii::app()->config->stripe[$isLiveMode ? 'liveSecretKey' : 'testSecretKey'];
				
		\Stripe\Stripe::setApiKey($privateKey);
		Yii::log('Stripe: token:'.$token.', $key: '.$privateKey, CLogger::LEVEL_INFO, 'toxus.stripe');
		try {
			$info = array(
				'amount' => $amount,
				'currency' => Yii::app()->config->stripe['currency'],
				'source' => $token,
				'description' => Yii::app()->config->stripe['description']
			);
			$charge = \Stripe\Charge::create($info);
			Yii::log('Stripe returns: '. $charge, CLogger::LEVEL_INFO, 'toxus.stripe');
			return true;
		} catch (Exception $e) {
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'toxus.stripe');
			return false;	
		}
	}
}