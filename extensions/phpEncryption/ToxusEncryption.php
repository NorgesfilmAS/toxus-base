<?php
/* 
 * Encryption and decryption of strings
 *  version 1.0 2015-06-27
 * uses
 *			https://github.com/defuse/php-encryption 
 */

require_once(dirname(__FILE__).'/src/Crypto.php');
require_once(dirname(__FILE__).'/src/ExceptionHandler.php');
require_once(dirname(__FILE__).'/src/Exception/CannotPerformOperation.php');
require_once(dirname(__FILE__).'/src/Exception/CryptoTestFailed.php');
require_once(dirname(__FILE__).'/src/Exception/InvalidCiphertext.php');
class ToxusEncryption extends CComponent
{
	/** 
	 * key should be generated and stored before using this class
	 *
	 * @var string
	 */
	private static $_key;
	
	public static function getKey()
	{
		return ToxusEncryption::$_key;
	}
	public static function setKey($value)
	{
		ToxusEncryption::$_key = $value;
	}
	/**
	 * generate a new key and stores / returns it
	 * 
	 * @return type string
	 */
	public static function generateKey()
	{
		ToxusEncryption::$_key = Defuse\Crypto\Crypto::createNewRandomKey();
		return ToxusEncryption::$_key;
	}
	/**
	 * 
	 * @param type $text
	 * @return the text or false on error
	 
	 */
	public static function crypt($text)
	{
		try {
			$text = Defuse\Crypto\Crypto::encrypt($text, ToxusEncryption::$_key);
		} catch (Exception $e) {
			return false;
		}	
		return $text;
	}
	public static function decrypt($text)
	{
		try {
			return Defuse\Crypto\Crypto::decrypt($text, ToxusEncryption::$_key);
		} catch (Exception $ex) {
			return false;	
		}
	}
}
	

