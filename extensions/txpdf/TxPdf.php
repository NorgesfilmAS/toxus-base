<?php
/**
 * extension on Tcpdf
 * 
 * Routes the subclass Header and Footer function into the parent handler function
 */

//require_once '';

	
class TxPdf extends TCPDF
{	
	public $onHeader;
	public $onFooter;
	
	public function Header() {
		if (isset($this->onHeader)) {
			call_user_func($this->onHeader);
		}
	}

	// Page footer
	public function Footer() {
		if (isset($this->onFooter)) {
			call_user_func($this->onFooter);
		}
	}
}
