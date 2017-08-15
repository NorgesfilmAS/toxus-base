<?php

class Util {
	
	static $monthNames = array(
			1 => 'Januari', 
			2 => 'Februari',
			3 => 'Maart',
			4 => 'April', 
			5 => 'Mei', 
			6 => 'Juni', 
			7 => 'Juli', 
			8 => 'Augustus', 
			9 => 'September', 
			10 =>	'Oktober', 
			11 => 'November', 
			12 =>	'December'				
		);
		
	
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
	/**
	 * generates a: 
	 *	cabc5c45-3e63-896d-cfdb-d60041b1fc3 
	 * string
	 */
	static function generateGuid()
	{
		$s = Util::generateRandomString(8).
						'-'.Util::generateRandomString(4).
						'-'.Util::generateRandomString(4).
						'-'.Util::generateRandomString(4).
						'-'.Util::generateRandomString(8);
		return strtolower($s);
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
		if (is_string($date)) {
			if (substr($date, 0, 4) < '2000') return '';
			$s = strtotime ($date);
		} else {
			$s = $date;
		}	
		return date (FormatDef::dateFormatPhp(), $s); //date ('d/m/Y', $s);
	}
	
	static function dateTimeToString($date)
	{
		if (is_object($date)) {
			return $date->format(DateTime::ISO8601);
		}
		return $date;
	}

	/**
	 * 
	 * @param date $date
	 * @return string: yyyy-mm-dd
	 */
	static function dateTimeToSqlString($date)
	{
		if (is_object($date)) {
			return $date->format('Y-m-d');
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
				'0' => ucfirst(Yii::t('base', 'no')),
				'1' => ucfirst(Yii::t('base', 'yes')),				
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
	
	static function proef11($bankrek){
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
				$s .= ', '.(is_array($error) ? implode(',', $error) : $error);
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
				if(isset($dt['year']) && !checkdate($dt['month'], $dt['day'], $dt['year']) ){
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
	
	/**
	 * reads a file as utf8 and calls decode utf8 and write the file with the filename.ut8
	 * @param string $filename the name of the file
	 */
	static function decodeUtf8File($filename)
	{
		if (!file_exists($filename)) {
			return false;
		}
		$s = file_get_contents($filename);
		$sDecode = utf8_decode($s);
		$fileInfo =  new FileInformation($filename);
		$name = $fileInfo->dirName.'/'.$fileInfo->name.'.utf.'.$fileInfo->extension;
		$l = 1;
		while (file_exists($name) ) {
			$name = $fileInfo->dirName.'/'.$fileInfo->name.'.utf.'.$l.'.'.$fileInfo->extension;
			$l++;
		}	
		return file_put_contents($name, $sDecode);
	}
	
/**
	 * reads a file as utf8 and calls decode utf8 and write the file with the filename.ut8
	 * @param string $filename the name of the file
	 */
	static function encodeUtf8File($filename)
	{
		if (!file_exists($filename)) {
			return false;
		}
		$s = file_get_contents($filename);
		$sDecode = utf8_encode($s);
		$fileInfo =  new FileInformation($filename);
		$name = $fileInfo->dirName.'/'.$fileInfo->name.'.utf.'.$fileInfo->extension;
		$l = 1;
		while (file_exists($name) ) {
			$name = $fileInfo->dirName.'/'.$fileInfo->name.'.utf.'.$l.'.'.$fileInfo->extension;
			$l++;
		}	
		return file_put_contents($name, $sDecode);
	}	
	
	static function uDate($format = 'Y-m-d H:i:s.u T', $utimestamp = null) 
	{
		if (is_null($utimestamp))
			$utimestamp = microtime(true);

		$timestamp = floor($utimestamp);
		$milliseconds = round(($utimestamp - $timestamp) * 1000000);

		return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
	}
	
	/**
	 * checks if an ip is in a range
	 * http://stackoverflow.com/questions/10421613/match-ipv4-address-given-ip-range-mask
	 * 
	 * does understand:
	 *   10.0.0.1
	 *   10.0.0.1-10.0.0.124
	 *   10.0.*.*
	 *	 10.0.0.1/16 (what does it mean?)
	 * 
	 * @param string $network
	 * @param string $ip
	 * @return boolean
	 */
	static function netMatch($network, $ip) {
    $network=trim($network);
    $orig_network = $network;
    $ip = trim($ip);
    if ($ip == $network) {
        Yii::log("used network ($network) for ($ip)", CLogger::LEVEL_INFO, 'toxus.util.netMatch');
        return TRUE;
    }
    $network = str_replace(' ', '', $network);
    if (strpos($network, '*') !== FALSE) {
        if (strpos($network, '/') !== FALSE) {
            $asParts = explode('/', $network);
            $network = @ $asParts[0];
        }
        $nCount = substr_count($network, '*');
        $network = str_replace('*', '0', $network);
        if ($nCount == 1) {
            $network .= '/24';
        } else if ($nCount == 2) {
            $network .= '/16';
        } else if ($nCount == 3) {
            $network .= '/8';
        } else if ($nCount > 3) {
            return TRUE; // if *.*.*.*, then all, so matched
        }
    }

    Yii::log("from original network($orig_network), used network ($network) for ($ip)", CLogger::LEVEL_INFO, 'toxus.util.netMatch');

    $d = strpos($network, '-');
    if ($d === FALSE) {
        $ip_arr = explode('/', $network);
        if (!preg_match("@\d*\.\d*\.\d*\.\d*@", $ip_arr[0], $matches)){
            $ip_arr[0].=".0";    // Alternate form 194.1.4/24
        }
        $network_long = ip2long($ip_arr[0]);
        $x = ip2long($ip_arr[1]);
        $mask = long2ip($x) == $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
        $ip_long = ip2long($ip);
        return ($ip_long & $mask) == ($network_long & $mask);
    } else {
        $from = trim(ip2long(substr($network, 0, $d)));
        $to = trim(ip2long(substr($network, $d+1)));
        $ip = ip2long($ip);
        return ($ip>=$from and $ip<=$to);
    }	
	}
	
	/**
	 * generate a filename out of a string
	 * 
	 * @param string $string
	 * @param bool $force_lowercase
	 * @param bool $anal If set to *true*, will remove all non-alphanumeric characters.
 	 * @return string
	 */
	static function sanitize($string, $force_lowercase = true, $anal = false, $noSpace=true) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
		if ($noSpace) {
			$clean = preg_replace('/\s+/', "-", $clean);		//tx: add the +
		}	
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
	}
	
	/**	
	 * removes all diacrit chacters by their closes
	 * 
	 * @param string $str
	 * @return string
	 */
	static function normalizeStr($str)
	{
    
    
		$invalid = array('Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z',
		'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A',
		'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
		'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
		'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',  'Ü' => 'U', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
		'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a',
		'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e',  'ë'=>'e', 'ì'=>'i', 'í'=>'i',
		'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
		'ö'=>'o', 'ø'=>'o', 'ü' => 'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y',  'ý'=>'y', 'þ'=>'b',
		'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', "`" => "'", "´" => "'", "„" => ",", "`" => "'",
		"´" => "'", "“" => "\"", "”" => "\"", "´" => "'", "&acirc;€™" => "'", "{" => "",
		"~" => "", "–" => "-", "’" => "'");

		$str = str_replace(array_keys($invalid), array_values($invalid), $str);
    $str = preg_replace('/[^\x20-\x7E]/', '', $str); // remove all other characters
		return $str;
	}
	
	/**
	 * date : format is yyyy-mm-dd
	 * time : format is hh:mm:ss
	 */
	static function dateAndTime2Timestamp($date, $time = null)
	{
		// fix if there are no time given
		if ($time && $time !== ':00') {
			if (strlen($time) == 5) {  // should include the seconds
				$time .= ':01';
			}
			$s = $date.' '.$time;			
		} else {	
			$s = $date.' 00:00:01';			
		}	
		$d = DateTime::createFromFormat('Y-m-d H:i:s', $s);
		return date_timestamp_get($d);
	}
	
	// http://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case
	static function fromCamelCase($input) 
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
	
	// http://stackoverflow.com/questions/2791998/convert-dashes-to-camelcase-in-php
	static function underscoreToCamelCase($string, $capitalizeFirstCharacter = false) 	{

    $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

    if (!$capitalizeFirstCharacter) {
        $str[0] = strtolower($str[0]);
    }

    return $str;
}
	
	/**
	 * change the file extension
	 * 
	 * @param string $filename
	 * @param string $newExtension extension without the .
	 * @return string
	 */
	static function fileChangeExtension($filename, $newExtension) {
    $info = pathinfo($filename);
    return $info['dirname'].'/'. $info['filename'] . '.' . $newExtension;
	}
	
	static function rows2models($rows, $className, $order = false)
	{
		$result = array();
		foreach ($rows as $row) {
			$a = $className::model()->findByPk($row['id']);
			if ($order) {
				$result[$a->$order] = $a;
			} else {
				$result[$row['id']] = $a;
			}
		}
		if ($order) {
			ksort($result);
		}
		return $result;
	}

	
	/**
	 * Tries to get a lock on the file. If it does not work it return false
	 * 
	 * @param integer $fp handle to the file
	 * @return boolean true: lock | false: no lock possbile
	 */
	static function fileGetLock($fp)
	{
		$cnt = 0;
		// try to get a lock on the file
		while ( !$fp || !flock($fp,LOCK_EX|LOCK_NB,$eWouldBlock) || $eWouldBlock ) {  // acquire an exclusive lock
			usleep(rand(1,100) * 10);
			$cnt ++;
			if ($cnt > 100) {
				return false;
			}	
		}
		return true;
	}
	
	/**
	 * the wait lock version of file_get_contents
	 * 
	 * @param string $filename
	 */
	static function fileGetContents($filename)
	{
		if(!file_exists($filename)) {
      return '';
    } else {
			$fp = fopen($filename, 'r');
			if (!Util::fileGetLock($fp)) {
				return false;
			}
			$cts = file_get_contents($filename);
			flock($fp, LOCK_UN);
			fclose($fp);
			return $cts;
    } 		
	}
	static function filePutContents($filename, $contents, $mode = 0777)
	{
		if (!file_exists($filename)) {
			$fp = fopen($filename, "w+");
			if ($mode) {
				$setRights = true;
			}
		} else {
			$fp = fopen($filename, "r+");
			$setRights = false;
		}	
		if (!Util::fileGetLock($fp)) {
			return false;
		}
		ftruncate($fp, 0);      // truncate file
		fwrite($fp, $contents);
		fflush($fp);            // flush output before releasing the lock
		flock($fp, LOCK_UN);    // release the lock
		fclose($fp);		
		if ($setRights) {
			chmod($filename, $mode);
		}
		return true;
	}
	
	static function sizeText($size)
	{
		$bytes = sprintf('%u', $size);

		if ($bytes > 0)	{
			$unit = intval(log($bytes, 1024));
			$units = array('B', 'KB', 'MB', 'GB', 'TB');

			if (array_key_exists($unit, $units) === true)	{
				return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
			}
		}
		return $bytes.' B';		
	}
	
	/**
	 * Finds the occurance of the string in the haystack, checking for escaped strings
	 * 
	 * @param type $haystack
	 * @param type $string
	 * @param string $escape the letter to use to escape
	 * @return false|integer the position
	 */
	static function strposEscape($haystack, $string, $escape='\\')
	{
		$l = strpos($haystack, $string);
		while ($l) {
			if ($l > 0 && substr($haystack, $l-1, 1) != $escape) {
				return $l;
			}
			$l = strpos($haystack, $string, $l++);
		}
		return false;
	}
	
	static function array2NameArray($array, $columnNames)
	{
		if (count($columnNames) < 2)	{
			throw new CException(Yii::t('app', 'The array2NameArray excepts atleats 2 column names'));
		}
		$result = array();
		foreach ($array as $key => $element) {
			$result[] = array($columnNames[0] => $key, $columnNames[1] => $element);			
		}
						
		return $result;
	}
	/**
	 * change the array keys so the first character is lower case and string is camel case
	 * @param array $arr
	 */
	static function properArrayKeys($arr) 
	{
		$result = array();
		foreach ($arr as $key => $value) {
			$result[Util::underscoreToCamelCase($key)] = $value;
		}
		return $result;
	}
	
	static function explodeClean($delimiter, $string) {
		$result = explode($delimiter, $string);
		foreach ($result as $k => $r) {
			if (empty($r)) {
				unset($result[$k]);
			}
		}
		return $result;
	}
	/*
	 * http://stackoverflow.com/questions/14674834/php-convert-string-to-hex-and-hex-to-string
	 */
	static function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
	}
	static  function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
  }
  
  /**
   * a rough none safe version to make debug sql   
   */
  static function mergeSqlParams($sql, $params=array()) {
    foreach ($params as $key => $param) {
      $sql = str_replace($key, '"'.$param.'"', $sql);
      
    }
    return $sql;
  }

  /**
   * deep compares two array if something has changed
   * 
   * @param type $arr1
   * @param type $arr2
   * @return boolean
   */
  /*
  static function arrayDiff($arr1, $arr2) {
    foreach ($arr1 as $key1 => $elm1) {
      if (!array_key_exists($key1, $arr2)) {
        return true;
      } elseif (is_array($elm1) && array_key_exists ($key1, $arr2) && is_array($arr2[$elm1])) {
        return Util::arrayDiff($elm1, $arr2[$key1]);        
      } elseif (is_array($elm1)) {
        return true;
      } elseif ($elm1 != $arr2[$key1]) {
        return true;        
      }
    }
    return false;
  }
  */
  static function arrayDiff($aArray1, $aArray2) {
  $aReturn = array();

  foreach ($aArray1 as $mKey => $mValue) {
    if (array_key_exists($mKey, $aArray2)) {
      if (is_array($mValue)) {
        $aRecursiveDiff = Util::arrayDiff($mValue, $aArray2[$mKey]);
        if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
      } else {
        if ($mValue != $aArray2[$mKey]) {
          $aReturn[$mKey] = $mValue;
        }
      }
    } else {
      $aReturn[$mKey] = $mValue;
    }
  }
  return $aReturn;
} 
  /**
   * compares two arrays and returns an array with the changed root keys
   * it does compare the arrays deep.
   * 
   * @param array $arr1
   * @param array $arr2
   * @returns array
   *    [add]     => array(key arr2, key arr2, ...)
   *    [remove]  => array(key arr1, key arr1, ...)
   *    [changed] => array(key arr1, key arr1, ...)
   *    
   */
  static function arrayChanged($arr1, $arr2, $options = array('merge' => false)) {
    $return = array(
      'add' => array(),
      'remove' => array(),
      'changed' => array()
    );
    foreach ($arr1 as $key1 => $elm1) {
      if (!array_key_exists($key1, $arr2)) {
        $return['add'][] = $key1;
      } else {
        if (!is_array($elm1)) {
          if ($elm1 != $arr2[$key1]) {
            $return['changed'][] = $key1;
          }
        } else {
          if (count(Util::arrayDiff($arr2[$key1], $elm1)) > 0) {
            $return['changed'][] = $key1;
          }
        }
      }
    }
    foreach ($arr2 as $key2 => $elm2) {
      if (!array_key_exists($key2, $arr1)) {
        $return['removed'][] = $key2;
      }
    }
    if ($options['merge']) {
      return array_merge($return['add'], $return['remove'], $return['changed']);
    }
    return $return;
  }
}

?>
