<?php
include('../header.php');
include_once('../../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);

/**
 * Retreive recurring payment profile by profile identifier. Profile identifier should be stored after profile creation
 * and can also be obtained by logging in to your Paypal account.
 */

// Recurring payment profile identifier
$profileId = isset($_GET['id']) ? $_GET['id'] : 'I-XH6WBEB75WP9';
$details = $paypal -> getRecurringProfile($profileId);


// Details found
if(is_array($details)) : 
	?>
	<h2>Recurring profile details</h2>
	<pre>
	<?php 
	var_dump($details);
	?>
	</pre>

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
<?php include('../footer.php'); ?>