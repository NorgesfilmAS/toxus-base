<?php
include_once('../library/Lionite/Paypal.php');

//Set sandbox mode
Lionite_Paypal::sandbox(true);

$payments = array(
    0 => array(
        'email' => 'johndoe@gmail.com',
        'amount' => '49.99',
        'note' => 'Payment for services rendered'
    ),
    1 => array(
        'email' => 'janedoe@yahoo.com',
        'amount' => '35.99',
        'note' => 'Website design payment'
    )
);
$paypal = new Lionite_Paypal();
$result = $paypal -> masspay($payments,'Lionite payment sent');
var_dump($result);
$errors = $paypal -> getErrors();
var_dump($errors);