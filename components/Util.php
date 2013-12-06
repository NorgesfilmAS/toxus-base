<?php

class Util {
	
	/**
	 *
	 * Generate a random string for a slug usage
	 * 
	 * 
	 * @param int $length
	 * @return string 
	 */
	static function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $string;
	}
	
	static function unique()
	{
		return Util::generateRandomString(30);
	}
	/**
	 * Convert a mySQL (2012-12-24 18:00:34) string into  php datetime
	 *  
	 * @param string $text 
	 */
	static function stringToDate($text, $options = array())
	{
		$params = array_merge(
						array(
							'seperator' => '-'	
						), 
						$options);
		
		$year = substr($text, 0, 4) + 0;
		$month = substr($text, 5, 2) + 0;
		$day = substr($text, 8, 2) + 0;
		if ($year == 0 && $month == 0 && $day == 0)
			return null;
		return $day.$params['seperator'].$month.$params['seperator'].$year;
	}

	static function dateDisplay($date)
	{
		if (substr($date, 0, 4) < '2000') return '';
		$s = strtotime ($date);
		return date (formatDef::dateFormatPhp(), $s); //date ('d/m/Y', $s);
	}
	
	static function dateTimeToString($date)
	{
		if (is_object($date)) {
			return $date->format(DateTime::ISO8601);
		}
		return $date;
	}
	
	/**
	 * convert a string of currency (english notation) into european notation
	 * @param string $currency number with decimal:'.' thoutand: ','
	 */
	static function currencyToDisplay($currency)
	{
		if ($currency == null || trim($currency) == '') return '0'.FormatDef::decimalPoint().'00';
		$ret = str_replace(',', '', $currency);	// remove the thousant
		$parts = explode('.', $ret);
		if (!isset($parts[1]) || $parts[1] == '')
			$parts[1] = '00';
		elseif (strlen($parts[1]) > 2) {
			$parts[1] = substr($parts[1], 0, 2);
		} elseif (strlen($parts[1])  == 1) {
			$parts[1] .= '0';
		}	
		return $parts[0].FormatDef::decimalPoint().$parts[1];
	}
	/**
	 * convert an display (europe) version for currency into the english notation
	 * @param string $value
	 */
	static function stringToCurrency($value)
	{
		$ret = str_replace(FormatDef::thousandSeperator(),'', $value);		
		return str_replace(FormatDef::decimalPoint(), '.', $ret);
	}
	
	/**
	 * convert the dd[sep]mm[sep]yyyy format to mm dd yyyy format
	 * 
	 * @param string $text 
	 */
	static function dateToSQL($text)
	{
		if (strlen($text) >=10 ) {
			if (!is_numeric(substr($text,2,1))) { // xx-xx-2012
				$day = substr($text, 0, 2);
				$month = substr($text,3, 2 );
				$year = substr($text, 6, 4);							
				return $year.'-'.$month.'-'.$day;
			} elseif (!is_numeric(substr($text,4,1))) { // 2012-xx-xx
				return $text;
			}	
		}	
		return null;	
	}
	
	static function param($name, $default = null)
	{
		if (isset(Yii::app()->params[$name])) {
			return Yii::app()->params[$name];
		}
		return $default;
	}
	static function dataYesNo()
	{
		return array(
				'0' => ucfirst(Yii::t('general', 'no')),
				'1' => ucfirst(Yii::t('general', 'yes')),				
		);
	}
	
	/**
	 * return true if one of the attributes is chekced
	 * 
	 * @param CActiveRecord $model
	 * @param array $attributes r
	 */
	static function isOneChecked($model, $attributes)
	{
		foreach ($attributes as $attribute) {
			if ($model->$attribute != 0)
				return true;			
		}
		return false;			
	}

	static function AddElement($key, $value, $arr)
	{
		return array_merge(
						array($key => $value),
						$arr);
	}

	/**
	 * retrieves the server from the email address
	 * email can be: jaap van der kreeft <jaap@toxus.nl>
	 * or jaap@toxus.nl
	 * 
	 * @param string $email 
	 */
	static function serverFromEmail($email)
	{
		$server = explode('@', $email);
		if (isset($server[1])) {
		  $s = explode('>', $server[1]);
			if (isset($s[0]))
				return $s[0];
		}
		return null;
	}
	
	/**
	 * check what kind of page this is.
	 * 
	 * returns: offer, search-for or nothing
	 */
	static function pageType()
	{
		$url = Yii::app()->request->getUrl();		
		if (strpos($url, 'profiel')) return 'default';
		if (strpos($url, 'aanvrager')) return 'offer';
		if (strpos($url, 'aanbieder')) return 'search-for';
		if (isset($_POST['SearchForm']) && isset($_POST['SearchForm']['searchSupplier']))	{
		   return $_POST['SearchForm']['searchSupplier'] == '1' ? 'offer' : 'search-for';
		}		
		if (Yii::app()->user->getState('searchSupplier') != null) {
			return Yii::app()->user->getState('searchSupplier') == '1' ? 'offer' : 'search-for';
		}
		return '';
	}
	
	
	static function formatAmount($amount)
	{
		$values = explode('.', $amount);
		$return = $values[0].',';
		if (isset($values[1])) {
		  $return .= str_pad($values[1], 2, '0');	
		} else {
			$return .= '00';
		}
		return $return;
	}
	
	function proef11($bankrek){
		$csom = 0;                            // variabele initialiseren
		$pos = 9;                             // het aantal posities waaruit een bankrekeningnr hoort te bestaan
		for ($i = 0; $i < strlen($bankrek); $i++){
			$num = substr($bankrek,$i,1);       // bekijk elk karakter van de ingevoerde string
			if ( is_numeric( $num )){           // controleer of het karakter numeriek is
				$csom += $num * $pos;                        // bereken somproduct van het cijfer en diens positie
				$pos--;                           // naar de volgende positie
			}
		}
		$postb = ($pos > 1) && ($pos < 7);    // True als resterende posities tussen 1 en 7 => Postbank
		$mod = $csom % 11;                                        // bereken restwaarde van somproduct/11.
		return( $postb || !($pos || $mod) );  // True als het een postbanknr is of restwaarde=0 zonder resterende posities
	}
	
	// http://www.cosninix.com/wp/2007/07/acceptgiros-printen/
	static function paymentReference($default='') 
	{
		// generate a 15 char number
		$length = 15;
    $characters = '0123456789';
    $numbers = $default;    
    while (strlen($numbers) < $length) {
        $numbers .= $characters[mt_rand(0, strlen($characters))];
    }
		// $numbers = strrev('098911021201240');//098911021201240
		// string is now 15 numbers:
		$weging = array(0 => 2, 1=> 4, 2=> 8, 3=> 5, 4=> 10, 5=> 9, 6=> 7, 7=> 3, 8=> 6, 9=>1);
		$sum = 0;
		$cnt = 0;
		$numbers = strrev($numbers);
		for ($cnt = 0; $cnt < strlen($numbers) ; $cnt ++) {
			$l = $cnt % 10;
			$w = $weging[$l];
			$sum += $numbers[$cnt] * $w ;
		}
		$rest = $sum % 11;
		$controle .= (11 - $rest) % 10;
		return $controle.strrev($numbers);
	}
	
	/**
	 * @return string the javascript init for the date format
	 */
	static function dateConfig()
	{
		return "{
			format: 'd/m/Y',
			days: ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'],
			months: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December']
			}";
	}
	
	
	/**
	 * Delete all files and directories in the path. Path will remain valid
	 * @param string $path
	 * info from: http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
	 */
	static function delTree($path)
	{
		foreach (new DirectoryIterator($path) as $fileInfo) {
			if (!$fileInfo->isDot()) {
				if ($fileInfo->isFile()) {
					unlink($path.'/'.$fileInfo->getFilename());		
				} else if ($fileInfo->isDir()) {
					self::delTree($path.'/'.$fileInfo->getFilename());
					rmdir($path.'/'.$fileInfo->getFilename());
				}	
			}
		}	
	}
	
	static function errorToString($errors, $isHtml = false)
	{
					
		$s = '';
		if ($isHtml) {
			foreach ($errors as $error) {
				$s .= '<br />'.CHtml::encode(implode(', ', $error));
			}
			return substr($s, 6);
		} else {
			foreach ($errors as $error) {
				$s .= ', '.implode(',', $error);
			}
			return substr($s, 2);
		}
	}
	
	/**
	 * generates a key that one bigger than the given
	 * 
	 * @param string $key
	 */
	static function nextSortKey($key)
	{
		$rev = strrev($key);
		$ch = substr($key,0, 1);
		$ch = Chr(Ord($ch) + 1);
		return strrev(substr($key, 1)).$ch;
	}
	
	static function date_parse_from_format($format, $date) {
		// reverse engineer date formats
		$keys = array(
				'Y' => array('year', '\d{4}'),              //Année sur 4 chiffres
				'y' => array('year', '\d{2}'),              //Année sur 2 chiffres
				'm' => array('month', '\d{2}'),             //Mois au format numérique, avec zéros initiaux
				'n' => array('month', '\d{1,2}'),           //Mois sans les zéros initiaux
				'M' => array('month', '[A-Z][a-z]{3}'),     //Mois, en trois lettres, en anglais
				'F' => array('month', '[A-Z][a-z]{2,8}'),   //Mois, textuel, version longue; en anglais, comme January ou December
				'd' => array('day', '\d{2}'),               //Jour du mois, sur deux chiffres (avec un zéro initial)
				'j' => array('day', '\d{1,2}'),             //Jour du mois sans les zéros initiaux
				'D' => array('day', '[A-Z][a-z]{2}'),       //Jour de la semaine, en trois lettres (et en anglais)
				'l' => array('day', '[A-Z][a-z]{6,9}'),     //Jour de la semaine, textuel, version longue, en anglais
				'u' => array('hour', '\d{1,6}'),            //Microsecondes
				'h' => array('hour', '\d{2}'),              //Heure, au format 12h, avec les zéros initiaux
				'H' => array('hour', '\d{2}'),              //Heure, au format 24h, avec les zéros initiaux
				'g' => array('hour', '\d{1,2}'),            //Heure, au format 12h, sans les zéros initiaux
				'G' => array('hour', '\d{1,2}'),            //Heure, au format 24h, sans les zéros initiaux
				'i' => array('minute', '\d{2}'),            //Minutes avec les zéros initiaux
				's' => array('second', '\d{2}')             //Secondes, avec zéros initiaux
		);

		// convert format string to regex
		$regex = '';
		$chars = str_split($format);
		foreach ( $chars AS $n => $char ) {
				$lastChar = isset($chars[$n-1]) ? $chars[$n-1] : '';
				$skipCurrent = '\\' == $lastChar;
				if ( !$skipCurrent && isset($keys[$char]) ) {
						$regex .= '(?P<'.$keys[$char][0].'>'.$keys[$char][1].')';
				}
				else if ( '\\' == $char ) {
						$regex .= $char;
				}
				else {
						$regex .= preg_quote($char);
				}
		}

		$dt = array();
		$dt['error_count'] = 0;
		// now try to match it
		if( preg_match('#^'.$regex.'$#', $date, $dt) ){
				foreach ( $dt AS $k => $v ){
						if ( is_int($k) ){
								unset($dt[$k]);
						}
				}
				if( !checkdate($dt['month'], $dt['day'], $dt['year']) ){
						$dt['error_count'] = 1;
				}
		}
		else {
				$dt['error_count'] = 1;
		}
		$dt['errors'] = array();
		$dt['fraction'] = '';
		$dt['warning_count'] = 0;
		$dt['warnings'] = array();
		$dt['is_localtime'] = 0;
		$dt['zone_type'] = 0;
		$dt['zone'] = 0;
		$dt['is_dst'] = '';
		return $dt;
  }		
	
	/**
	 * fills the number with leading 0
	 * 
	 * @param the number $value
	 * @param integer $threshold
	 * @return string
	 */
	
	static function addLeadingZero($value, $threshold = 2) {
    return sprintf('%0' . $threshold . 's', $value);
	}

	/**
	 * replace multiple spaces with a single space
	 * @param string $text
	 * @return string
	 */
	static function trimSpaces($text)
	{
		while (strpos($text,"  ") !== false) 	{
			$text=str_replace("  "," ",$text);
		}
		return trim($text);
	}

	static function formId($form)
	{
		return md5(json_encode($form));
	}
	
	
	static function substringIndex($str, $delim, $count)
	{
    if ($count < 0){
      return implode($delim, array_slice(explode($delim, $str), $count));
    } else {
      return implode($delim, array_slice(explode($delim, $str), 0, $count));
    }
	}
	
	/**
	 * converts the 16M to the number of bytes 
	 * 
	 * @param string @value
	 */
	static function bytesToCount($value)
	{
    if ( is_numeric( $value ) ) {
      return $value;
    } else {
			$value_length = strlen( $value );
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
						$qty *= 1024;
						break;
				case 'm':
						$qty *= 1048576;
						break;
				case 'g':
						$qty *= 1073741824;
						break;
			}
			return $qty;
		}	
	}
	
	static function maxFileUploadSize($inBytes = true)
	{
		$value = ini_get( 'upload_max_filesize' );
		if (!$inBytes) return $value;
		return Util::bytesToCount($value);
	}
	static function maxPostSize($inBytes = true)
	{
		$value = ini_get('post_max_size');		
		if (!$inBytes) return $value;
		return Util::bytesToCount($value);

	}
	
}

?>
