<?php
/**
 * IMPORTANT
 * 
 * In order to test this code you need a Website Payments Pro Paypal account (live or sandbox). 
 * You need to configure the Lionite_Paypal class with your API details
 */

include('header.php');
include_once('../library/Lionite/Paypal.php');

//Set sandbox mode
Lionite_Paypal::sandbox(true);

//Payment form was submitted
if(isset($_POST['card_type'])) :
	$items = array();
	/**
	 * In this example, we expect a POST parameter called 'cart'. 
	 * $_POST['cart'] contains an array of items - the key being the ID and the value the amount.
	 * We iterate over this array to build the items array for the payment, using product data we fetch from our application (products.php) 
	 */
	include('products.php');
	foreach($_POST['cart'] as $id => $amount) {
		$product = $products[$id];
		if($amount > 0) {
			$items[] = array(
				'cost' => $product['price'],
				'amount' => $amount,
				'name' => $product['name'],
				'number' => $id
			);
		}
	}
	
	// In this example payment options are sent from the included example form.
	// You can set those options programmatically if necessary.
	$options = $_POST;
	$options['currency'] = 'GBP'; // You can set additional options, as needed

	/**
	 * Before we submit we can perform fraud detection using Lionite_Minfraud -
	 * http://www.binpress.com/app/maxmind-minfraud-api-wrapper/1058
	 *
	 * Uncomment to run example code if you have the Minfraud component as well
	 */

	/*
	include_once('../library/Lionite/Minfraud.php');
	$minfraud = new Lionite_Minfraud();
	$result = $minfraud ->checkDirectPayment($_POST);

	// Result contains plenty of interesting information you might want to log
	// The most important is the Risk Score which is an approximate chance of fraud
	// Pick a risk score that makes sense to you based on experience or estimate:

	$maxRiskScore = 50; // Example value, change to fit your purposes
	if($result['riskScore'] > $maxRiskScore) {
		 // Handle fraud. Some options:

		 // 1. Hold transaction for manual review.
		 // 2. Show an error message asking user to use an alternative payment method and avoid using proxies.
		 // 3. Ban user IP and session ID.

		 // Read more at - http://www.binpress.com/blog/2012/07/31/fighting-online-fraud-pitfalls-and-solutions/
	}
	**/

	$paypal = new Lionite_Paypal();
	$result = $paypal -> directPayment($options,$items);

	if(is_string($result)) : 
		//Successful payment, $result is the transaction ID
		$transactionId = $result;
	else : 
		//Some details are missing, show error messages 
		?>
		<h3>Your payment was not processed:</h3>
		<?php $errors = $paypal -> getErrors(); ?>
		<ul class="errors">
		<?php foreach($errors as $error) : ?>
			<li><?php echo $error; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php endif; ?>

<?php if(isset($transactionId)) : ?>
<h1>Payment received</h1>
<p>Paypal transaction ID is <b><?php echo $transactionId; ?></b>. Please copy it for future reference.</p>
<?php else : ?>
<h2>Direct Payment demo</h2>
<p>Mock data is entered into the form for your convenience (will work on the sandbox).</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="payment">
	<input type="hidden" name="method" value="direct" />
	<?php foreach ($_POST['cart'] as $id => $amount) : ?>
		<?php if($amount > 0) : ?>
		<input type="hidden" name="cart[<?php echo $id; ?>]" value="<?php echo $amount; ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
	<fieldset class="details">
		<h3>Personal information</h3>
		<br />
		<label>First name<br />
			<input name="first_name" tabindex="1" type="text" class="text" value="John" />		
		</label>
		
		<label>Last name<br />
			<input name="last_name" tabindex="2" type="text" class="text" value="Doe" />		
		</label>

		
		<label>Country<br />
			<select name="country" id="country" tabindex="3">
			    <option value="AF" label="Afghanistan">Afghanistan</option>
			    <option value="AL" label="Albania">Albania</option>
			    <option value="DZ" label="Algeria">Algeria</option>
			    <option value="AS" label="American Samoa">American Samoa</option>
			
			    <option value="AD" label="Andorra">Andorra</option>
			    <option value="AO" label="Angola">Angola</option>
			    <option value="AI" label="Anguilla">Anguilla</option>
			    <option value="AQ" label="Antarctica">Antarctica</option>
			    <option value="AG" label="Antigua And Barbuda">Antigua And Barbuda</option>
			    <option value="AR" label="Argentina">Argentina</option>
			
			    <option value="AM" label="Armenia">Armenia</option>
			    <option value="AW" label="Aruba">Aruba</option>
			    <option value="AU" label="Australia">Australia</option>
			    <option value="AT" label="Austria">Austria</option>
			    <option value="AZ" label="Azerbaijan">Azerbaijan</option>
			    <option value="BS" label="Bahamas">Bahamas</option>
			
			    <option value="BH" label="Bahrain">Bahrain</option>
			    <option value="BD" label="Bangladesh">Bangladesh</option>
			    <option value="BB" label="Barbados">Barbados</option>
			    <option value="BY" label="Belarus">Belarus</option>
			    <option value="BE" label="Belgium">Belgium</option>
			    <option value="BZ" label="Belize">Belize</option>
			
			    <option value="BJ" label="Benin">Benin</option>
			    <option value="BM" label="Bermuda">Bermuda</option>
			    <option value="BT" label="Bhutan">Bhutan</option>
			    <option value="BO" label="Bolivia">Bolivia</option>
			    <option value="BA" label="Bosnia And Herzegovina">Bosnia And Herzegovina</option>
			    <option value="BW" label="Botswana">Botswana</option>
			
			    <option value="BV" label="Bouvet Island">Bouvet Island</option>
			    <option value="BR" label="Brazil">Brazil</option>
			    <option value="BN" label="Brunei Darussalam">Brunei Darussalam</option>
			    <option value="BG" label="Bulgaria">Bulgaria</option>
			    <option value="BF" label="Burkina Faso">Burkina Faso</option>
			    <option value="BI" label="Burundi">Burundi</option>
			
			    <option value="KH" label="Cambodia">Cambodia</option>
			    <option value="CM" label="Cameroon">Cameroon</option>
			    <option value="CA" label="Canada">Canada</option>
			    <option value="CV" label="Cape Verde">Cape Verde</option>
			    <option value="KY" label="Cayman Islands">Cayman Islands</option>
			    <option value="CF" label="Central African Republic">Central African Republic</option>
			
			    <option value="TD" label="Chad">Chad</option>
			    <option value="CL" label="Chile">Chile</option>
			    <option value="CN" label="China">China</option>
			    <option value="CX" label="Christmas Island">Christmas Island</option>
			    <option value="CC" label="Cocos (keeling) Islands">Cocos (keeling) Islands</option>
			    <option value="CO" label="Colombia">Colombia</option>
			
			    <option value="KM" label="Comoros">Comoros</option>
			    <option value="CG" label="Congo">Congo</option>
			    <option value="CK" label="Cook Islands">Cook Islands</option>
			    <option value="CR" label="Costa Rica">Costa Rica</option>
			    <option value="CI" label="Cote D Ivoire">Cote D Ivoire</option>
			    <option value="HR" label="Croatia">Croatia</option>
			
			    <option value="CU" label="Cuba">Cuba</option>
			    <option value="CY" label="Cyprus">Cyprus</option>
			    <option value="CZ" label="Czech Republic">Czech Republic</option>
			    <option value="DK" label="Denmark">Denmark</option>
			    <option value="DJ" label="Djibouti">Djibouti</option>
			    <option value="DM" label="Dominica">Dominica</option>
			
			    <option value="DO" label="Dominican Republic">Dominican Republic</option>
			    <option value="TP" label="East Timor">East Timor</option>
			    <option value="EC" label="Ecuador">Ecuador</option>
			    <option value="EG" label="Egypt">Egypt</option>
			    <option value="SV" label="El Salvador">El Salvador</option>
			    <option value="GQ" label="Equatorial Guinea">Equatorial Guinea</option>
			
			    <option value="ER" label="Eritrea">Eritrea</option>
			    <option value="EE" label="Estonia">Estonia</option>
			    <option value="ET" label="Ethiopia">Ethiopia</option>
			    <option value="FK" label="Falkland Islands">Falkland Islands</option>
			    <option value="FO" label="Faroe Islands">Faroe Islands</option>
			    <option value="FJ" label="Fiji">Fiji</option>
			
			    <option value="FI" label="Finland">Finland</option>
			    <option value="FR" label="France">France</option>
			    <option value="GF" label="French Guiana">French Guiana</option>
			    <option value="PF" label="French Polynesia">French Polynesia</option>
			    <option value="GA" label="Gabon">Gabon</option>
			    <option value="GM" label="Gambia">Gambia</option>
			
			    <option value="GE" label="Georgia">Georgia</option>
			    <option value="DE" label="Germany">Germany</option>
			    <option value="GH" label="Ghana">Ghana</option>
			    <option value="GI" label="Gibraltar">Gibraltar</option>
			    <option value="GR" label="Greece">Greece</option>
			    <option value="GL" label="Greenland">Greenland</option>
			
			    <option value="GD" label="Grenada">Grenada</option>
			    <option value="GP" label="Guadeloupe">Guadeloupe</option>
			    <option value="GU" label="Guam">Guam</option>
			    <option value="GT" label="Guatemala">Guatemala</option>
			    <option value="GN" label="Guinea">Guinea</option>
			    <option value="GW" label="Guinea-bissau">Guinea-bissau</option>
			
			    <option value="GY" label="Guyana">Guyana</option>
			    <option value="HT" label="Haiti">Haiti</option>
			    <option value="VA" label="Holy See">Holy See</option>
			    <option value="HN" label="Honduras">Honduras</option>
			    <option value="HK" label="Hong Kong">Hong Kong</option>
			    <option value="HU" label="Hungary">Hungary</option>
			
			    <option value="IS" label="Iceland">Iceland</option>
			    <option value="IN" label="India">India</option>
			    <option value="ID" label="Indonesia">Indonesia</option>
			    <option value="IR" label="Iran">Iran</option>
			    <option value="IQ" label="Iraq">Iraq</option>
			    <option value="IE" label="Ireland">Ireland</option>
			
			    <option value="IL" label="Israel">Israel</option>
			    <option value="IT" label="Italy">Italy</option>
			    <option value="JM" label="Jamaica">Jamaica</option>
			    <option value="JP" label="Japan">Japan</option>
			    <option value="JO" label="Jordan">Jordan</option>
			    <option value="KZ" label="Kazakstan">Kazakstan</option>
			
			    <option value="KE" label="Kenya">Kenya</option>
			    <option value="KI" label="Kiribati">Kiribati</option>
			    <option value="KR" label="Korea Republic Of">Korea Republic Of</option>
			    <option value="KW" label="Kuwait">Kuwait</option>
			    <option value="KG" label="Kyrgyzstan">Kyrgyzstan</option>
			    <option value="LV" label="Latvia">Latvia</option>
			
			    <option value="LB" label="Lebanon">Lebanon</option>
			    <option value="LS" label="Lesotho">Lesotho</option>
			    <option value="LR" label="Liberia">Liberia</option>
			    <option value="LY" label="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
			    <option value="LI" label="Liechtenstein">Liechtenstein</option>
			    <option value="LT" label="Lithuania">Lithuania</option>
			
			    <option value="LU" label="Luxembourg">Luxembourg</option>
			    <option value="MO" label="Macau">Macau</option>
			    <option value="MK" label="Macedonia">Macedonia</option>
			    <option value="MG" label="Madagascar">Madagascar</option>
			    <option value="MW" label="Malawi">Malawi</option>
			    <option value="MY" label="Malaysia">Malaysia</option>
			
			    <option value="MV" label="Maldives">Maldives</option>
			    <option value="ML" label="Mali">Mali</option>
			    <option value="MT" label="Malta">Malta</option>
			    <option value="MH" label="Marshall Islands">Marshall Islands</option>
			    <option value="MQ" label="Martinique">Martinique</option>
			    <option value="MR" label="Mauritania">Mauritania</option>
			
			    <option value="MU" label="Mauritius">Mauritius</option>
			    <option value="YT" label="Mayotte">Mayotte</option>
			    <option value="MX" label="Mexico">Mexico</option>
			    <option value="FM" label="Micronesia">Micronesia</option>
			    <option value="MD" label="Moldova, Republic Of">Moldova, Republic Of</option>
			    <option value="MC" label="Monaco">Monaco</option>
			
			    <option value="MN" label="Mongolia">Mongolia</option>
			    <option value="MS" label="Montserrat">Montserrat</option>
			    <option value="MA" label="Morocco">Morocco</option>
			    <option value="MZ" label="Mozambique">Mozambique</option>
			    <option value="MM" label="Myanmar">Myanmar</option>
			    <option value="NA" label="Namibia">Namibia</option>
			
			    <option value="NR" label="Nauru">Nauru</option>
			    <option value="NP" label="Nepal">Nepal</option>
			    <option value="NL" label="Netherlands">Netherlands</option>
			    <option value="AN" label="Netherlands Antilles">Netherlands Antilles</option>
			    <option value="NC" label="New Caledonia">New Caledonia</option>
			    <option value="NZ" label="New Zealand">New Zealand</option>
			
			    <option value="NI" label="Nicaragua">Nicaragua</option>
			    <option value="NE" label="Niger">Niger</option>
			    <option value="NG" label="Nigeria">Nigeria</option>
			    <option value="NU" label="Niue">Niue</option>
			    <option value="NF" label="Norfolk Island">Norfolk Island</option>
			    <option value="MP" label="Northern Mariana Islands">Northern Mariana Islands</option>
			
			    <option value="NO" label="Norway">Norway</option>
			    <option value="OM" label="Oman">Oman</option>
			    <option value="PK" label="Pakistan">Pakistan</option>
			    <option value="PW" label="Palau">Palau</option>
			    <option value="PS" label="Palestinian Territory">Palestinian Territory</option>
			    <option value="PA" label="Panama">Panama</option>
			
			    <option value="PG" label="Papua New Guinea">Papua New Guinea</option>
			    <option value="PY" label="Paraguay">Paraguay</option>
			    <option value="PE" label="Peru">Peru</option>
			    <option value="PH" label="Philippines">Philippines</option>
			    <option value="PN" label="Pitcairn">Pitcairn</option>
			    <option value="PL" label="Poland">Poland</option>
			
			    <option value="PT" label="Portugal">Portugal</option>
			    <option value="PR" label="Puerto Rico">Puerto Rico</option>
			    <option value="QA" label="Qatar">Qatar</option>
			    <option value="RE" label="Reunion">Reunion</option>
			    <option value="RO" label="Romania">Romania</option>
			    <option value="RU" label="Russian Federation">Russian Federation</option>
			
			    <option value="RW" label="Rwanda">Rwanda</option>
			    <option value="SH" label="Saint Helena">Saint Helena</option>
			    <option value="KN" label="Saint Kitts And Nevis">Saint Kitts And Nevis</option>
			    <option value="LC" label="Saint Lucia">Saint Lucia</option>
			    <option value="WS" label="Samoa">Samoa</option>
			    <option value="SM" label="San Marino">San Marino</option>
			
			    <option value="ST" label="Sao Tome And Principe">Sao Tome And Principe</option>
			    <option value="SA" label="Saudi Arabia">Saudi Arabia</option>
			    <option value="SN" label="Senegal">Senegal</option>
			    <option value="SC" label="Seychelles">Seychelles</option>
			    <option value="SL" label="Sierra Leone">Sierra Leone</option>
			    <option value="SG" label="Singapore">Singapore</option>
			
			    <option value="SK" label="Slovakia">Slovakia</option>
			    <option value="SI" label="Slovenia">Slovenia</option>
			    <option value="SB" label="Solomon Islands">Solomon Islands</option>
			    <option value="SO" label="Somalia">Somalia</option>
			    <option value="ZA" label="South Africa">South Africa</option>
			    <option value="GS" label="South Georgia">South Georgia</option>
			
			    <option value="ES" label="Spain">Spain</option>
			    <option value="LK" label="Sri Lanka">Sri Lanka</option>
			    <option value="SD" label="Sudan">Sudan</option>
			    <option value="SR" label="Suriname">Suriname</option>
			    <option value="SJ" label="Svalbard And Jan Mayen">Svalbard And Jan Mayen</option>
			    <option value="SZ" label="Swaziland">Swaziland</option>
			
			    <option value="SE" label="Sweden">Sweden</option>
			    <option value="CH" label="Switzerland">Switzerland</option>
			    <option value="SY" label="Syrian Arab Republic">Syrian Arab Republic</option>
			    <option value="TW" label="Taiwan">Taiwan</option>
			    <option value="TJ" label="Tajikistan">Tajikistan</option>
			    <option value="TZ" label="Tanzania">Tanzania</option>
			
			    <option value="TH" label="Thailand">Thailand</option>
			    <option value="TG" label="Togo">Togo</option>
			    <option value="TK" label="Tokelau">Tokelau</option>
			    <option value="TO" label="Tonga">Tonga</option>
			    <option value="TT" label="Trinidad And Tobago">Trinidad And Tobago</option>
			    <option value="TN" label="Tunisia">Tunisia</option>
			
			    <option value="TR" label="Turkey">Turkey</option>
			    <option value="TM" label="Turkmenistan">Turkmenistan</option>
			    <option value="TC" label="Turks And Caicos Islands">Turks And Caicos Islands</option>
			    <option value="TV" label="Tuvalu">Tuvalu</option>
			    <option value="UG" label="Uganda">Uganda</option>
			    <option value="UA" label="Ukraine">Ukraine</option>
			
			    <option value="AE" label="United Arab Emirates">United Arab Emirates</option>
			    <option value="GB" label="United Kingdom">United Kingdom</option>
			    <option value="US" label="United States" selected="selected">United States</option>
			    <option value="UY" label="Uruguay">Uruguay</option>
			    <option value="UZ" label="Uzbekistan">Uzbekistan</option>
			    <option value="VU" label="Vanuatu">Vanuatu</option>
			
			    <option value="VE" label="Venezuela">Venezuela</option>
			    <option value="VN" label="Viet Nam">Viet Nam</option>
			    <option value="VG" label="Virgin Islands, British">Virgin Islands, British</option>
			    <option value="VI" label="Virgin Islands, U.s.">Virgin Islands, U.s.</option>
			    <option value="WF" label="Wallis And Futuna">Wallis And Futuna</option>
			    <option value="EH" label="Western Sahara">Western Sahara</option>
			
			    <option value="YE" label="Yemen">Yemen</option>
			    <option value="YU" label="Yugoslavia">Yugoslavia</option>
			    <option value="ZM" label="Zambia">Zambia</option>
			    <option value="ZW" label="Zimbabwe">Zimbabwe</option>
			</select>		
		</label>
		
		<label class="city">City<br />
			<input name="city" tabindex="4" type="text" class="text" value="New York" />
		</label>
		
		<label class="zip">Zip<br />
			<input name="zipcode" tabindex="5" type="text" class="text" value="10010" />		
		</label>
						
		<label>State / Prov<br />
			<input name="state" tabindex="6" type="text" class="text" value="NY" />		
		</label>
						
		<label>Street address<br />
			<input name="street" tabindex="7" type="text" class="text" value="14 Argyle Rd." /> 
		</label>
		
		<label>Address 2<br />
			<input name="street2" tabindex="8" type="text" class="text" /> 
		</label>
				
		<label>Phone / Mobile number<br />
			<input name="phone" tabindex="9" type="text" class="text" /> 
		</label>
		
	</fieldset>

	
	<fieldset class="credit_card">
		<h3>Credit card information</h3><br />
		<label>Card type : <br />
		<select name="card_type" id="card_type">
		    <option value="Visa" label="Visa" selected="selected">Visa</option>
		    <option value="MasterCard" label="Master Card">Master Card</option>
		    <option value="Discover" label="Discover">Discover</option>
		    <option value="Amex" label="American Express">American Express</option>
		</select>		
		</label>
		
		<label>Card number :<br />
			<input name="card_number" type="text" class="text" value="4929802607281663" />		
		</label>
						
		<label class="expiry-label">Expiry date : </label>
		<label class="cvv-label">CVV </label>

        <select name="expiry_month" id="expiry_month" style="clear:left;">
        	<?php for($i = 1; $i < 13; $i++) : ?>
            <option <?php echo $i == 12 ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>" label="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
    	</select>
    	<select name="expiry_year" id="expiry_year">
			<?php for($i = date('Y'); $i < date('Y') + 6; $i++) : ?>
		    <option <?php echo $i == 2013 ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>" label="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>        
		<input name="cvv" type="text" class="text cvv" value="545" />       										
		
		<button type="submit">Confirm payment</button>
	</fieldset>
</form>
<?php include('footer.php'); ?>
<?php endif; ?>