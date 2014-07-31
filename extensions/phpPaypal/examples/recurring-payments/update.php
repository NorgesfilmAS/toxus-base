<?php
include('../header.php');
include_once('../../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);

/**
 * Update recurring payment profile details
 */

// Recurring payment profile identifier
$profileId = isset($_GET['id']) ? $_GET['id'] : 'I-TEG7RABB0MGK';

// Update recurring profile details. Details are passed in the same format as when creating the profile
$data = array(
	'cost' => '31.99',
	'currency' => 'GBP' // Currency must match currency of the recurring profile
);

$result = $paypal -> updateRecurringProfile($profileId,$data);

// Change successful
if($result) : 
	?>
	<h2>Profile details updated successfully.</h2>

<?php 

// Failure - show errors
else : ?>
<p>Paypal reported back with the following message : </p>
<ul class="errors">
<?php $errors = $paypal -> getErrors(); ?>
<?php foreach($errors as $errNum => $error) : ?>
	<li><?php echo '[' . $errNum . '] ' . $error; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<p>[See update.php for more details]</p>
<?php include('../footer.php'); ?>