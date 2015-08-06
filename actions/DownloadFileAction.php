<?php
/**
 * streams a file to the client
 * 
 */
Yii::import('toxus.actions.BaseAction');
class DownloadFileAction extends BaseAction
{
	/**
	 * the full path to the file
	 * @var string
	 */	
	public $path = false;
	
	/**
	 * show the download options to the user
	 * 
	 * @var boolean
	 */
	public $forceDownload = false;
	/**
	 * the name the user will see when downloading or false to use the default name
	 * @var string / false
	 */
	public $userFilename = false;
	
	/**
	 * translate the name to a fysical filename
	 * 
	 * @var string
	 */
	public $onGetFilename = false;  // function ($name, $action)
					
	/** 
	 * 
	 * 
	 * @param string $name the name of the file
	 * @throws CHttpException
	 */

	public function run($name='')
	{
    if ($this->afterLoadModel) {
      call_user_func($this->afterLoadModel, null);
    }		    
		$logKey = 'toxus.download';
		Yii::log('Downloading: '.$name, CLogger::LEVEL_INFO, $logKey);
		$this->checkRights();
		if ($name == '') {
			if (count($_GET) > 0) {
				$name = reset($_GET);
				$key = key($_GET);			
				if ($name != '') {
					$name = key($_GET).'/'.$name;
				} else {
					$name = $key;
				}
			}
		}		
		if ($this->onGetFilename) {
			$filename = call_user_func($this->onGetFilename, $name, $this);	
		} else {
			if (file_exists($this->path) && is_file($this->path)) {
				$filename = $this->path;
			} else {
				if (substr($this->path,0,1) == '@') {
					$filename = YiiBase::getPathOfAlias(substr($this->path,1)).'/'.$name;
				} else {	
					$filename = $this->path.$name;
				}	
			}
		}	
    
		Yii::log('filename: '.$filename, CLogger::LEVEL_INFO, $logKey);		
		$ff = new FileInformation($filename);
		if (!$ff->exists()) {
			throw new CHttpException(404, 'File not found');
		}
		if ($this->userFilename === false) {
			$this->userFilename = $ff->filename;
		}
		set_time_limit(0);
		$filesize = $ff->size; // filesize($filename);    
    ///////////////////////////////////
    /*
    header("X-Sendfile: ".$filename);
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="' . $this->userFilename . '"');    
    return ;
     * 
     */
		////////////////////////////////////////


		$offset = 0;
		$length = $filesize;
		if ( isset($_SERVER['HTTP_RANGE']) ) {
			// if the HTTP_RANGE header is set we're dealing with partial content
			$partialContent = true;
			// find the requested range
			// this might be too simplistic, apparently the client can request
			// multiple ranges, which can become pretty complex, so ignore it for now
			preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
			$offset = intval($matches[1]);
			$length = count($matches) > 2 ? (intval($matches[2]) - $offset) : 0;
		} else {
			$partialContent = false;
		}		
		Yii::log('opening file', CLogger::LEVEL_INFO, $logKey);		
		
		$file = @fopen($ff->path, "rb");
		fseek($file, $offset);
		if ( $partialContent ) {
			header('HTTP/1.1 206 Partial Content');
      $s = 'Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $filesize;
			header($s);
		}
		if ($this->forceDownload) {
			header('Content-disposition: attachment; filename='.$this->userFilename);
		}	
		header('Content-type: '.$ff->contentType);
		header('Content-Length: ' . $filesize);
		header('Accept-Ranges: bytes');
		header_remove("X-Powered-By");
		
    $bCount = 0;    
    if ($length > 0) {
/*      
      echo file_get_contents($filename);
*/    
      $chunkSize = 1024*1024;
      try {
        while(!@feof($file) && $bCount < $length )	{
          $b = fread($file, $chunkSize); //1024*8);
          echo $b;
          @ob_flush();
          @flush();
          $bCount += strlen($b);
          Yii::log('Send: '.$bCount.' bytes', CLogger::LEVEL_INFO, 'toxus.download');
        }		
      } catch (Exception $e) {
        Yii::log('Error: '.$e->getMessage(), CLogger::LEVEL_ERROR, $logKey);		
      }
    }  
		@fclose($file);		
		Yii::log('done', CLogger::LEVEL_INFO, $logKey);		
	}
}