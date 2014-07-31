<?php
include('../header.php');
include_once('../../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);

/**
 * Create a recurring payment profile
 * Note: Trial period is optional, you can remove those parameters if it is not needed
 */

$date = date('c',time() + 7 * 24 * 3600); // Use the date you want recurring payments to start

$profile = array(
	'trial_period' => 'Month', // Trial period interval
	'trial_frequency' => 1, // Trial billing frequency 
	'trial_total_cycles' => 1, // Trial period total payment cycles
	'trial_cost' => 0, // Trial amount. We have a free trial in this example
	
	'period' => 'Month', // Subscription Period
	'frequency' => 1, // Frequency x period equals 1 billing cycle (up to 1 year)
	'total_cycles' => 12,  // End profile after 1 year (12 cycles)
	'cost' => '29.99', // Regular subscription amount

	'desc' => 'Time magazine monthly subscription for 1 year. 1 month free trial, then 29.99 GBP monthly', // Same as description given at Express Checkout start
	'start_date' => $date, // Profile start date
	'currency' => 'GBP' // Payment currency
);

$profileId = $paypal ->createRecurringProfile($profile);

// Recurring profile created successfully
if(is_string($profileId)) {
	// $profileId is the recurring profile ID. It should be stored for future reference and for usage with other recurring profile methods. See other examples that us it, as in the following links

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

<p>Note that you can only confirm a transaction once. If you refresh this page, the token will become invalid and will
	result in an error.</p>

<?php include('../footer.php'); ?>