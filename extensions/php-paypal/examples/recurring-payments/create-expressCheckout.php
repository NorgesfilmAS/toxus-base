<?php 
/**
 * IMPORTANT
 * 
 * In order to test this code you need an active Paypal account (live or sandbox). 
 * You need to configure the Lionite_Paypal class with your API details
 * 
 * - If you want to test the Direct Payment method, you need a Website Payments Pro account (either live or sandbox)
 */
 
include_once('../../library/Lionite/Paypal.php');

/**
 * Setup is similar to Express Checkout, since it uses the same method to start the process.
 * The main difference is the subscription item we use to create a recurring payment profile.
 *
 * Actual subscription details are set when creating the recurring profile, see confirm-expressCheckout.php
 */

$base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

// Defining payment options
$options = array(
	'return' => $base . '/confirm-expressCheckout.php', // Confirmation URL
	'cancel' => $base . '/' // Cancellation URL
);

/**
 * Our subscription item
 */
$item = array(
	'subscription_description' => 'Time magazine monthly subscription for 1 year. 1 month free trial, then 29.99 GBP monthly'
);

Lionite_Paypal::sandbox(true); // Testing in sandbox mode
$paypal = new Lionite_Paypal();
$result = $paypal -> getCheckoutUrl($options,$item);

if(is_string($result)) :
	//Redirection URL generated successfully! redirecting to Paypal
	header('Location: ' . $result);
else : 
//Token generation failed - show error messages
include('../header.php'); 
?>
<p>There was a problem processing your payment:</p>
<ul class="errors">
<?php $errors = $paypal -> getErrors(); ?>
<?php foreach($errors as $errNum => $error) : ?>
	<li><?php echo '[' . $errNum . '] ' . $error; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>