<?php
include('../header.php');
include_once('../../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);

/**
 * Change recurring payment profile status 
 */

// Recurring payment profile identifier
$profileId = isset($_GET['id']) ? $_GET['id'] : 'I-XH6WBEB75WP9';

// Change profile status. Possible values include 'Suspend','Cancel','Reactivate'. See method comments for further details
$status = isset($_GET['status']) ? $_GET['status'] : 'Suspend';
$result = $paypal -> changeRecurringProfileStatus($profileId,$status);

// Change successful
if($result) : 
	?>
	<h2>Profile status updated successfully.</h2>
	<p>Last operation: <b><?php echo $status; ?></b></p>
	<?php
	$url = 'status-change.php?id=' . $profileId . '&status=';
	if($status == 'Suspend') : ?>
	<a href="<?php echo $url; ?>Reactivate">Reactivate</a> | <a href="<?php echo $url; ?>Cancel">Cancel</a>
	<?php elseif($status == 'Reactivate') : ?>
	<a href="<?php echo $url; ?>Suspend">Suspend</a> | <a href="<?php echo $url; ?>Cancel">Cancel</a>
	<?php endif; 

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
<p>[See status-change.php for more details]</p>
<?php include('../footer.php'); ?>