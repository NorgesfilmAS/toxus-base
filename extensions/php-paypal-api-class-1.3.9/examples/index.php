<?php 
/**
 * IMPORTANT
 * 
 * In order to test this code you need an active Paypal account (live or sandbox). 
 * You need to configure the Lionite_Paypal class with your API details
 * 
 * - If you want to test the Direct Payment method, you need a Website Payments Pro account (either live or sandbox)
 */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$empty = true;
	foreach($_POST['cart'] as $id => $amount) {
		if($amount > 0) {
			$empty = false;
			break;
		}
	}
}
if(isset($empty) && !$empty) :
	//Handle cart submission
	include_once($_POST['method'] == 'checkout' ? 'checkout.php' : 'direct.php');
else : 
include('header.php'); ?>
<h1>Lionite Paypal API Examples</h1>
<?php include_once('products.php'); ?>

<h2>Your shopping cart</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellpadding="0" cellspacing="0">
	<tr>
		<th>#</th>
		<th>Product</th>
		<th>Price</th>
		<th>Amount</th>
	</tr>
	<?php foreach($products as $product) : ?>
	<tr>
		<td><?php echo $product['id']; ?></td>
		<td><?php echo $product['name']; ?></td>
		<td><?php echo number_format($product['price'],2,'.',','); ?></td>
		<td><input type="text" class="text number" name="cart[<?php echo $product['id']; ?>]" value="0" /></td>
	</tr>	
	<?php endforeach; ?>
</table>
<?php if(isset($empty) && $empty) : ?>
<div class="notice">Please add at least 1 item</div>
<?php endif; ?>
	<label>Payment method</label>
	<input type="radio" name="method" value="checkout" checked="checked" /> Express Checkout<br />
	<input type="radio" name="method" value="direct" /> Direct Payment<br />
	<button type="submit" class="checkout">PAY</button>
</form>
<?php endif; ?>
<?php include('footer.php'); ?>