<?php
/**
 * wrapper for converting HTML generated by Yii into a PDF
 * 
 * version 0.01 2012-10-15 Toxus / Jaap van der Kreeft
 * version 0.1  Uses events to generate the header and footer
 * 
 */


class TxPdfComponent extends CApplicationComponent {
	public $isLandscape = false; 
	public $units = 'mm';  // allowed: pt", "in", "cm" or "mm"
	public $pageSize = 'A4'; // allowed: 'A3', 'A4', 'A5', 'Letter', 'Legal' of The format must be array(w, h)
	public $unicode = true;
	public $encoding = 'UTF-8';
	
	public $creator = 'TxPdf'; 
	public $author = 'Yii';
	public $title = '';
	public $subject = '';
	public $keywords = '';
	
	// fonts
	public $fontMonospace = 'courier';
	public $font = array('family' => 'dejavusans', 'style' => '', 'size' => 12, 'file' => '','subset' => '' );
	
	public $margins = array('left' => 15, 'top' => 15, 'right' => 15, 'bottom' => 15, 'header' => 5, 'footer' => 15);
	public $autoPageBreak = true;
	
	
	public $language = 'eng';		// see: tcpdf/config/lang for allowed values
	
	/** 
	 * unknow but 'usefull ;) properties
	 */
	public $imageScaleRation = '1.25'; //??????
	public $fontSubsetting = true;
	
	
	public $cssFilename = '';	// to use an external css
	public $cssFilenameExtension = '.css';
	
	// the path to the image directory
	// format allowed: application.views.images
	//                  /images   (relative to views    
	public $imagePath = 'application.views.images';	  
	
	
	public $headerHtml = null;	
	public $footerHtml = null;	
	public $content= '';
	
	protected $_tcpdf = null;		
	
	
	public function init()
	{
		parent::init();				

		$tcpdf = $this->tcpdf; // load it
		$this->_tcpdf->onHeader = array($this, 'generateHeader');
		$this->generateHeader();
		$this->_tcpdf->onFooter = array($this, 'generateFooter');		
	}
	
	/**
	 * do all init statements
	 * Can't use the $this->tcpdf because it's still undefined.
	 */
	protected function initPdf()
	{
		$this->onAfterConstruct(new TxPdfEvent($this));
		
		$this->_tcpdf->setCreator($this->creator);
		$this->_tcpdf->SetAuthor($this->author);
		$this->_tcpdf->SetTitle($this->title);
		$this->_tcpdf->SetSubject($this->subject);
		$this->_tcpdf->SetKeywords($this->keywords);
		
		$this->_tcpdf->SetDefaultMonospacedFont($this->fontMonospace);		
		
		$this->_tcpdf->SetMargins($this->margins['left'], $this->margins['top'], $this->margins['right']);
		$this->_tcpdf->SetFooterMargin($this->margins['footer']);
		$this->_tcpdf->SetAutoPageBreak($this->autoPageBreak, $this->margins['bottom']);
		
		$this->_tcpdf->setImageScale($this->imageScaleRation);
		$this->_tcpdf->setLanguageArray($this->language);		
		$this->_tcpdf->setFontSubsetting($this->fontSubsetting);
		$this->_tcpdf->setFont($this->font['family'], $this->font['style'], $this->font['size'], $this->font['file'], $this->font['subset']);		
		$this->_tcpdf->AddPage();
	}
	
	
	public function onAfterConstruct($event)
	{
		$this->raiseEvent('onAfterConstruct', $event);
	}
	/**
	 * Called to generate the header
	 * 
	 * @param TxPdfEvent $event  
	 */
	public function onHeader($event)
	{
		$this->raiseEvent('onHeader', $event);
	}
	/**
	 * called to generate the footer
	 * 
	 * @param TxPdfEvent $event 
	 */
	public function onFooter($event)
	{
		$this->raiseEvent('onFooter', $event);
	}
	public function onImage($event)
	{
		$this->raiseEvent('onImage', $event);
	}
	
	/**
	 * function called by txPdf if the header should be generated
	 */
	public function generateHeader()
	{
		$event = new TxPdfEvent($this);
		$this->onHeader($event);
		if (! empty($event->html)) {
			$this->_tcpdf->writeHTML($event->html, true, false, true, false, '');			
		}
		if (!empty($this->headerHtml)) {
			$this->_tcpdf->writeHTML($this->headerHtml, true, false, true, false, '');
		}
	}
	/**
	 * function is called when footer is needed
	 */
	public function generateFooter()
	{
		$event = new TxPdfEvent($this);
		$this->onFooter($event);
		if (! empty($event->html)) {
			$this->_tcpdf->writeHTML($event->html, true, false, true, false, '');			
		}		
		if (!empty($this->footerHtml)) {
			$this->_tcpdf->writeHTML($this->footerHtml, true, false, true, false, '');
		}
		
	}
	
	/**
	 * resolves the name of the css file to include
	 * @return string 
	 */
	protected function resolveCssFile()
	{
		$filename = $this->cssFilename;
		if(empty($filename))
			return false;
		
		$extension = $this->cssFilenameExtension;
		$basePath = Yii::app()->getViewPath();
		
		if($filename[0]==='/') {
			$filename = $basePath.$filename;
		}	else if(strpos($filename,'.'))
			$filename = Yii::getPathOfAlias($filename);
		else
			$viewFile=$basePath.DIRECTORY_SEPARATOR.$filename;

		if(is_file($viewFile.$extension))
			return $viewFile.$extension;
		else
			return false;		
	}
	
	/**
	 * resolves the path where the images are stored
	 * 
	 * @return string of false
	 */
	protected function resolveImagePath()
	{
		$path = $this->imagePath;
		if(empty($path))
			return false;		
		
		$basePath = Yii::app()->getViewPath();
		
		if($path[0]==='/') {
			$path = $basePath.$path;
		}	else if(strpos($path,'.'))
			$path = Yii::getPathOfAlias($path);
		else
			$path=$basePath.DIRECTORY_SEPARATOR.$path;

		if (is_dir($path))
			return $path;
		else
			return false;				
	}

	/**
	 * the internal routine that generates the PDF
	 */
	
	protected function generatePdf()
	{
		$css = "";
		if ($this->cssFilename != '') {
			$cssFilename = $this->resolveCssFile();
			if ($cssFilename === false) 
				throw new CException('The cssFile ('.$this->cssFilename.' does not exist.');			
			$css = '<style>'.file_get_contents($cssFilename).'</style>';
		}
		$this->tcpdf->writeHTML($css.$this->content, true, false, true, false, '');
		$this->tcpdf->lastPage();
	}
	
	/**
	 * outputs the file direc to the user
	 *  
	 * @param string $filename the name display to the user
	 * @param boolean $inline  if true the page is rendered in the browser
	 */
	public function render($filename = '', $inline = true)
	{
		$this->generatePdf();
		$dest = array(true => 'I', false => 'D');
		$this->tcpdf->Output($filename, $dest[$inline]);
	}
	/**
	 * writes the file to disk
	 * 
	 * @param string $filename the name of the file on disk
	 */
	public function saveAs($filename = '')	
	{
		$this->generatePdf();
	  $this->tcpdf->Output($filename, 'F');
	}
	
	/**
	 * access to the lowlevel interal tcpdf
	 * 
	 * @return TCPDF 
	 */
	public function gettcpdf()
	{
		if (empty($this->_tcpdf)) {
			$dir = dirname(__FILE__);
			$alias = md5($dir);
			Yii::setPathOfAlias($alias,$dir);

			Yii::import($alias.'.*');
			Yii::import($alias.'.tcpdf.*');
			
			$pageDirection = array(false => 'P', true => 'L');						
			$this->_tcpdf = new TxPdf($pageDirection[$this->isLandscape], $this->units, $this->pageSize, $this->unicode, $this->encoding);
			$this->initPdf();
		}	
		return $this->_tcpdf;
	}
	
	/**
	 * returns the path to where the images are
	 * 
	 * @return string
	 */
	public function imageUrl($imageFile)
	{
		return $this->resolveImagePath().DIRECTORY_SEPARATOR.$imageFile;
	}
	
	/**
	 * change more then one margin at the time
	 *  
	 * @param array $values (top,right,bottom, left, header, footer)
	 */
	public function setMargins($values)
	{
		if (is_array($values)) {
			$this->margins = array_merge($this->margins, $values);
		}
	}

	/**
	 *  places an image on the document.
	 * 
	 * @param string $filename if $filename start with a / it's an absolute path, with a * it's an url otherwise relative to the imagesPath
	 * 
	 * @param array $options 
	 *							(x, y, width, height)
	 *							(align: [top, middle, bottom, newline]
	 *							(path)
	 * 
	 * 
	 */
	public function image($filename, $options=array())
	{
		$params = array_merge(
						array('left' => 0, 'top' => 0, 'width' => 20, 'height' => 20,
									'extension' => '', 'link' => '', 'align' => 'top',
								  'resize' => true, 'dpi'=> 300), 
						$options);
		$align = array('top' => 'T', 'middle' => 'M', 'bottom' => 'B', 'newline' => 'N');
		$resize = array('yes' => 'true', 'no' => false, 'scale' => 2);
		$name = '';

		$dir = dirname(__FILE__);
		$alias = md5($dir);
		Yii::setPathOfAlias($alias,$dir);
		Yii::import($alias.'.TxPdfEvent');
		
		$event = new TxPdfEvent($this);
		$event->filename = $filename;
		$this->onImage($event);
		if (isset($event->absolutePath))
			$name = $event->absolutePath;
		else {
			if (isset($options['path'])) {
				$filePath = Yii::getPathOfAlias($options['path']);
				if (is_dir($filePath)) {
					$name = $filePath.'/'.$filename;
				}	
			}
		}	
		if ($name == '') {
			if ($filename[0] !== '/' && $filename !== '*') {
				$filename = $this->resolveImagePath().DIRECTORY_SEPARATOR.$filename;
			}
		}	
		//$this->_tcpdf->Image($filename, $params['left'], $params['top'], $params['width'], $params['height'], 
		$this->tcpdf->Image($name, $params['left'], $params['top'], $params['width'], $params['height'], 
										$params['extension'], $params['link'], $align[$params['align']],
										$resize[$params['resize']], $params['dpi']	
									);
	}
	
	/**
	 * writes an HTML box on the current page
	 * 
	 * @param string $content the html content
	 * @param array $options to write
	 * 
	 * border -> true / false
	 * 
	 */
	public function html($content, $options = array())
	{
		$params = array_merge( array(
				'left' => 0, 'top' => 0, 'width' => 0, 'height' => 0,
				'border' => false, 
			), $options);
		

		$this->tcpdf->writeHTMLCell($params['width'], $params['height'], $parmas['left'], $params['top'], $content,
						$params['border']);
	}
	
}