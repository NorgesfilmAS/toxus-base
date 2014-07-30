<?php
include('header.php');
include_once('../library/Lionite/Paypal.php');
$paypal = new Lionite_Paypal();
//Set sandbox mode
Lionite_Paypal::sandbox(true);
$details = $paypal -> getCheckoutDetails($_GET['token']);

/**
 * Example fraud detection using Lionite_Minfraud
 * http://www.binpress.com/app/maxmind-minfraud-api-wrapper/1058
 *
 * Uncomment to run example code if you have the Minfraud component as well
 */

/*
include_once('../library/Lionite/Minfraud.php');
$minfraud = new Lionite_Minfraud();
$result = $minfraud -> checkExpressCheckout($details);

// Result contains plenty of interesting information you might want to log
// The most important is the Risk Score which is an approximate chance of fraud
// Pick a risk score that makes sense to you based on experience or estimate:

$maxRiskScore = 50; // Example value, change to fit your purposes
if($result['riskScore'] > $maxRiskScore) {
	 //
	 // Handle fraud. Some options:
 
	 // 1. Hold transaction for manual review.
	 // 2. Show an error message asking user to use an alternative payment method and avoid using proxies.
	 // 3. Ban user IP and session ID.

	 // Read more at - http://www.binpress.com/blog/2012/07/31/fighting-online-fraud-pitfalls-and-solutions/

}

**/
?>
<h2>Transaction details</h2>
<p>After the user has confirmed the transaction, we can get the full details of the purchase back from Paypal :</p>

<h3>Payment information (for example purposes, use discretion on a live site)</h3>
<div class="half">
<?php 
foreach($details as $key => $val) : ?>
	<?php if($key != 'items') : ?>
	<b><?php echo ucwords(str_replace('_',' ',$key)); ?></b> : 
	<?php echo $val; ?>
	<br />
	<?php endif; ?>
<?php endforeach; ?>
</div>
<?php if(!empty($details['items'])) : ?>
<div class="half">
	<b>Items</b> <br />
	<ol class="list">
		<?php foreach($details['items'] as $item) : ?>
		<li>
			<?php foreach($item as $itemKey => $itemVal) : ?>
				<?php if(!empty($itemVal)) : ?>
				<b><?php echo ucwords(str_replace('_',' ',$itemKey)); ?></b> : 
				
				<?php echo $itemVal; ?>
				<br />
				<?php endif; ?>
			<?php endforeach; ?>
		</li>
		<?php endforeach; ?>
	</ol>
</div>
<?php endif; ?>
<div class="clear"></div>

<h2>Transcation confirmation</h2>
<?php
$result = $paypal -> confirmCheckoutPayment();

if(is_string($result)) : 
//Completed successully!
?>
<br />
<b>Transaction ID</b> : <?php echo $result; ?>

<?php 

// Failure - show errors
else : ?>
<b>Transcation has been previously confirmed</b>
<p>Paypal reported back with the following message : </p>
<ul class="errors">
<?php $errors = $paypal -> getErrors(); ?>
<?php foreach($errors as $error) : ?>
	<li><?php echo $error; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php include('footer.php'); ?>