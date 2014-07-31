<?php 
/**
 * IMPORTANT
 * 
 * In order to test this code you need an active Paypal account (live or sandbox). 
 * You need to configure the Lionite_Paypal class with your API details. 
 */

include_once('../library/Lionite/Paypal.php');

// Transaction identifier
$transactionId = '5VY631383V0066111';

// Optional parameters, see method comments in class for details
$options = array(
	'refund_type' => 'Full'
);

Lionite_Paypal::sandbox(true); // Testing in sandbox mode
$paypal = new Lionite_Paypal();
$result = $paypal -> refund($transactionId,$options);
