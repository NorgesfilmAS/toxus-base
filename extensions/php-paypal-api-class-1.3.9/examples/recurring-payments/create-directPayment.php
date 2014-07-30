<?php
include('../header.php');
include_once('../../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);


/**
 * Create a recurring payment profile. In addition to the profile details, we pass the credit card information as well.
 */
$date = date('c',time() + 7 * 24 * 3600); 
$profile = array(	
	'period' => 'Month',
	'frequency' => 1, // Frequency x period equal 1 billing cycle (up to 1 year)
	'total_cycles' => 12, // End profile after 1 year (12 cycles)
	'cost' => '29.99',
	'desc' => 'Time magazine monthly subscription for 1 year', // Subscription description
	'start_date' => $date, // Profile start date
	'currency' => 'GBP', // Payment currency
	
	//Adding a trial period
	'trial_period' => 'Month',
	'trial_frequency' => 1,
	'trial_cost' => 0,
	'trial_total_cycles' => 1,
	
	// Credit card and payer details, see $_directPaymentDetails for more information
	'name' => 'Test User',
	'email' => 'test@test.com',
	'card_number' => '424242424242424242', // Credit card number
	'card_type' => 'Visa', // Credit card type
	'cvv' => '545', // Credit card security code (CVV)
	'expiry_date' => '052015', // Credit card expiry date
	'zipcode' => '10010', // Payer zipcode
	'city' => 'New York', // Payer city
	'country' => 'US', // Payer country
	'state' => 'NY', // Payer state (if US)
	'street' => '14 Argyle Rd.' // Payer street address
);

$profileId = $paypal ->createRecurringProfile($profile);

// Recurring profile created successfully
if(is_string($profileId)) {
	// $result is the recurring profile ID. It should be stored for future reference and for usage with
	// getRecurringProfileDetails()
	?>
	<br />
	<b>Recurring profile ID:</b> <?php echo $profileId; ?>
	<br />
	<a target="_blank" href="profile.php?id=<?php echo $profileId; ?>">View profile details example</a>
	<br />
	<a target="_blank" href="status-change.php?id=<?php echo $profileId; ?>">View status change example</a>
	<br />
	<a target="_blank" href="update.php?id=<?php echo $profileId; ?>">View profile update example</a>
	<?php
	
// Profile creation failed, handle / log errors
} else {
	$errors = $paypal -> getErrors();
	// Dumping errors for testing purposes only. Handle silently in production code
	echo 'Profile creation failed. Returned errors: '
		. '<pre>';
	var_dump($errors);
	echo '</pre>';
}
?>

<?php include('../footer.php'); ?>