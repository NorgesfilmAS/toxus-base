<?php
if(!class_exists('Lionite_Paypal')) {
	include_once('../library/Lionite/Paypal.php');
}
//Check if a POST request an the transaction type parameter is present
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['txn_type'])) {
	$path = dirname(__FILE__) . '/ipn.txt';
	$data = var_export($_POST,true);
	file_put_contents($path,$data);
	$paypal = new Lionite_Paypal();
	$result = $paypal -> confirmIpn($_POST);
	//If a Paypal transaction ID was returned
	if(is_string($result)) {
		//We update our transaction (in the database or otherwise) to indicate it is complete
	} 
}