<?php

class FormatDef extends CComponent
{
	static function vatOptions()
	{
		return array(
			'0' => '0',
			'6' => '6',
			'21' => '21',	
			);	
	}
	
	static function defaultVat()
	{
		return 21;
	}
	static function decimal()
	{
		return 2;
	}	
	/**
	 * if set to true the decimal point is an ,
	 */
	static function mustConvertNumber()
	{
		return true;
	}
	static function decimalPoint()
	{
		return ',';
	}
	static function thousandSeperator()
	{
		return '.';
	}
	
	static function currencySymbolHtml()
	{
		return '&euro;';
	}
	static function currencySymbol()
	{
		return '€';
	}
	static function currencyFormat()
	{
		return '{thousands:"'.FormatDev::thousandSeperator().'", decimal:"'.FormatDev::decimalPoint().',",symbol: ""}';
	}	
	static function dateFormat()
	{
		return 'dd-mm-yyyy';
	}
	static function dateFormatPhp()
	{
		return 'd-m-Y';
	}
		
}