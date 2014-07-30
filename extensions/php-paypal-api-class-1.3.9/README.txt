PHP PayPal API Class - Installation and troubleshooting
=======================================================

Please follow the following steps to get the PHP PayPal API class up and running on your webserver.

For full documentation, please visit the class page on Binpress - 
http://www.binpress.com/app/php-paypal-api-class/20

Setting up the class
====================

1. Get your API credentials from Paypal. You can find it by logging in to your account and going 
to 'Profile' -> 'API Access' (account information). Choose Option 2 - 'View API Signature'. 

2. Configure the /library/Lionite/Paypal.php and change the value of 'username', 'password' and 'signature'
under the $_settings['live'] array to the values in your PayPal account .

3. You can optionally use a file certificate instead of the signature. Paypal recommends using the signature when possible,
but due to specific constraints you might opt for the certificate. 

If you use a certificate, remove the 'signature' line from the $_settings array, and put the location of the paypal certificate
file in the $_PaypalCertificate parameter.

4. If you intend to use the sandbox for testing and development, you will need to create a sandbox account
and change the $_settings['sandbox'] values in a similar manner to the API signature from one of 
your sandbox accounts.

If you purchased this component as part of the bundle with the Maxmind Minfraud component, see minfraud-README.txt for more details.
http://www.binpress.com/app/maxmind-minfraud-api-wrapper/1058

Running the examples
====================

The examples use the PayPal sandbox by default. You will need to setup the PayPal class with 
the sandbox API credentials as defined in step 3. above.

* Direct payment examples (including recurring payments with a credit-card) requires that you 
use a website payments pro sandbox account. Otherwise you will receive an 
"invalid merchant configuration" error.

* Recurring payments examples are contained in an inner folder named '/examples/recurring-payments'

General Troubleshooting
=======================

Common issues that might occur when trying to use the class:

* cURL extension not installed. The class will throw an exception in this case, indicating the error.

* Certificate file not found or not readable in the /Cert folder relative to the Paypal.php class file. 
The class will throw an exception in this indicating the type of problem encountered. If the file is 
missing or corrupt, please copy it again to the correct location and if there is a permission problem, 
please set the group/owner to those used by the PHP process (often group/owner of the webserver process).

* "Security header is not valid"
Credentials not properly entered. The class errors array will contain error code 10002 with this message. 
Make sure you copied the credentials to the $_settings array as they appear 
in your PayPal account, and that you've put them in the correct account type (live / sandbox).


Submitting issue tickets
========================

If you are having other issues, please open a ticket on Binpress by going to your download history and 
clicking on "support" for the PHP PayPal API class component. Please go over the following before opening 
a ticket:

* Make sure you are running PHP at the highest error reporting level. You can either set it in you PHP 
configuration or by adding

    <?php error_reporting(E_ALL ^E_NOTICE); ?>

at the beginning of the script you are running. Any errors or notices appearing will be useful for
trying to solve your issue.
* Run <?php phpinfo(); ?> and save the results as an HTML file. Attach it to the ticket you submit.


License and copyright
=====================

PHP PayPal API Class is copyrighted by Lionite Ltd. You can use this package as specified by the license you 
acquired.