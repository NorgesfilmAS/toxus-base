<?php

/**
 * events raise by txPdf 
 * 
 */
class TxPdfEvent extends CEvent
{
//	public $internalPdf = null;	// the link to the tcPdf definition
	public $html = null;					// the html to insert at that point
	/**
	 * the filename to convert
	 * @var string
	 */
	public $filename = null;
	/**
	 * the absolute path to the file or false if not found
	 * @var string
	 */
	public $absolutePath = null;
	
	public function getComponent()
	{
		return $this->sender;
	}
	public function getPdf()
	{
		return $this->sender;
	}
	
	public function getPageCount()
	{
		$comp = $this->component;
		if ($comp)
			return $comp->tcpdf->pageCount();
		return 0;
	}
	public function getPageNumber()
	{
		$comp = $this->component;
		if ($comp)
			return $comp->tcpdf->pageNumber();
		return 0;
	}
}
