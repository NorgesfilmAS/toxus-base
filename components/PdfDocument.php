<?php
/**
 * the class to generate a pdf file
 */

Yii::import('toxus.extensions.txpdf.TxPdfComponent');

class PdfDocument extends TxPdfComponent {
	
	public function init()
	{
	}
	
	/**
	 * construct the basis of the PDF document, setting the margins.
	 * Margins can be set by the config key: pdf.margins => (top, right, bottom, left, footer)
	 * 
	 * @param type $event
	 * @return type
	 */
	static public function afterConstruct($event)
	{
		if (Yii::app()->config->value('pdf.margins')) {
			$margins = explode(',', Yii::app()->config->value('pdf.margins'));
			if (count($margins) != 5) {
				Yii::log('There should be 5 element is the pdf.margins (top, right, bottom, left, footer)', CLogger::LEVEL_ERROR, 'PdfFile.afterconstruct');
			}
			$event->pdf->setMargins(array(
				'top' => trim($margins[0]),
				'right' => trim($margins[1]),
				'bottom' => trim($margins[2]),
				'left' => trim($margins[3]),
				'footer' => trim($margins[4])	
			));
			return;
		} 
		$event->pdf->setMargins(array(
			'left' => 25,
			'top' => 55,
			'right' => 35,
			'bottom' => 10,
			'footer' => 25,
		));
	}
	
	public function getPdf() 
	{
		return Yii::app()->pdf;
	}
	
	/**
	 * parse the document into the actions to execute on the $pdf definition
	 * 
	 * 
	 * @param string $content
	 */
	protected function parse($document)
	{
		$fragments = explode("\nhtml(", $document);
		$steps = array();
		foreach ($fragments as $fragment) {
			$lines = explode("\n", $fragment);
			$html = trim($lines[0]);   // x, y )
			if ($html) {
				if (substr($html, -1) != ')') {
					Yii::log('The html statement is invalid: '.$html, CLogger::LEVEL_ERROR, 'PdfFile.parse');				
				} else {
					$html = substr($html, 0, -1);
					$pos = explode(',', $html);
					if (count($pos) != 2) {
						Yii::log('expecting 2 parameters for html: '.$html, CLogger::LEVEL_ERROR, 'PdfFile.parse');				
					} else {
						unset($lines[0]);
						$step = array(
							'html' => implode("\n", $lines),
							'position' => array(
								'left' => $pos[0],
								'top' => $pos[1]	
							)	
						);
						$steps[] = $step;
					}
				}	
			}
		}
		return $steps;
	}
	/**
	 * run the instrucions against the pdf document generating the document
	 */
	protected function generate($instructions)
	{
		$pdf = Yii::app()->pdf;
		foreach ($instructions as $inst) {
			$pdf->html($inst['html'], $inst['position']);
		}
	}
	
	/**
	 * 
	 * @param type $controller
	 * @param type $view
	 * @param type $data
	 */
	public function renderView($controller, $viewName, $data=array())
	{
		// render the twig file
		// ......
		$viewFile = YiiBase::getPathOfAlias('application').'/'. $controller->viewPath($viewName, array('directory' => 'pdf'));
		if(($renderer = Yii::app()->getViewRenderer()) !== null && $renderer->fileExtension === '.'.CFileHelper::getExtension($viewFile)) {
			$params = array_merge(
					array(
						'pdf' => $this,
						'viewName' => $viewName	
					),		
					$data
			);
			$content = $renderer->renderFile($controller, $viewFile, $params, true);
		} else {
			Yii::log('Could not find renderer for this type', CLogger::LEVEL_ERROR, 'PdfFile.render');
			return false;
		}		
		// parse the twig file into the
		$instructions = $this->parse($content);
		// generate
		$this->generate($instructions);
		
		return true;
	}
	
}