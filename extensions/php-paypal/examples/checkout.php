<?php 
/**
 * IMPORTANT
 * 
 * In order to test this code you need an active Paypal account (live or sandbox). 
 * You need to configure the Lionite_Paypal class with your API details. 
 *
 * This is a usage example only! it does not cover every possible use-case, 
 * nor should it be used as is in a production environment.
 */
if(!empty($_POST)) : 
	include_once('../library/Lionite/Paypal.php');
	
	//Set sandbox mode
	Lionite_Paypal::sandbox(true);
	
	/**
	 * In this example, we expect a POST parameter called 'cart'. 
	 * $_POST['cart'] contains an array of items - the key being the ID and the value the amount.
	 * We iterate over this array to build the items array for checking out, using product data we fetch from our application (products.php) 
	 */
	include_once('products.php');
	$items = array();
	foreach($_POST['cart'] as $id => $amount) {
		$product = $products[$id];
		if($amount > 0) {
			$items[] = array(
				'cost' => $product['price'],
				'amount' => $amount,
				'name' => $product['name'],
				'number' => $id,
				'tax' => 5.99
			);
		}
	}
	$base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

	// Defining payment options
	$options = array(
		'shipping' => 29.99,
		'handling' => 1.99,
		'return' => $base . '/confirm.php', // Confirmation URL
		'cancel' => $base . '/index.php', // Cancellation URL
		'allow_note' => 0,
		'useraction' => 'commit',
		'currency' => 'GBP', // Payment currency
		'custom' => 5 // A custom variable. Useful for identifying local entities, such as carts or products
	);

	$paypal = new Lionite_Paypal();
	$result = $paypal -> getCheckoutUrl($options,$items);
	
	if(is_string($result)) :
		//Redirection URL generated successfully! redirecting to Paypal
		header('Location: ' . $result);
	else : 
	//Token generation failed - show error messages
	include('header.php'); 
	?>
	<p>There was a problem processing your payment:</p>
	<ul>
	<?php 
	$errors = $paypal -> getErrors(); 
	foreach($errors as $error) : ?>
		<li><?php echo $error; ?></li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
<?php endif; ?>