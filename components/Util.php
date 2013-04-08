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
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
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
							'seperator' => '/'	
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
		return date ('d/m/Y', $s);
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
		$server = split('@', $email);
		if (isset($server[1])) {
		  $s = split('>', $server[1]);
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
	
	static function errorToString($errors)
	{
		$s = '';
		foreach ($errors as $error) {
			$s .= ', '.implode(',', $error);
		}
		return substr($s, 2);
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
}

?>
