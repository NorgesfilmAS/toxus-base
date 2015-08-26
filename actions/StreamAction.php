<?php
/* 
 * Replacement for the DownloadFileAction that streams a file to the user.
 * inspired by: https://groups.google.com/forum/#!msg/jplayer/nSM2UmnSKKA/bC-l3k0pCPMJ
 *              https://github.com/happyworm/smartReadFile/blob/master/smartReadFile.php    
 * 
 * version 2.0 jvk 2015-08-06
 * 
 */
Yii::import('toxus.actions.BaseAction');
class StreamAction extends BaseAction {
  
  public $path = false;
  
	public $forceDownload = false;
	/**
	 * translate the name to a fysical filename
	 * 
	 * @var string
	 */
	public $onGetFilename = false;  // function ($name, $action)

  /**
   * The name returned by the system
   * @var string 
   */
  public $userFilename = false;
  
  public function run() {
    
   if ($this->afterLoadModel) {
      call_user_func($this->afterLoadModel, null);
    }		    

    if ($this->onGetFilename) {
			$filename = call_user_func($this->onGetFilename, $this->path, $this);	
		} else {
      if (substr($this->path,0,1) == '@') {
        $filename = YiiBase::getPathOfAlias(substr($this->path,1)).'/'.$name;
      } else {	
        $filename = $this->path.$name;
      }	
		}	

    $fileInfo = new FileInformation($filename);
    if (!$fileInfo->exists()) { //(!file_exists($filename))	{
      Yii::log('The file '.$filename.' does not exist', CLogger::LEVEL_ERROR, 'toxus.StreamAction');
      throw new CHttpException(404, 'The file does not exist');
      //header ("HTTP/1.1 404 Not Found");
      //return;
    }

    $size	= $fileInfo->size; // filesize($filename);
    $time	= $fileInfo->date; // date('r', filemtime($filename));

    $fm		= @fopen($filename, 'rb');
    if (!$fm) {
      Yii::log('Can not open file '.$filename.'', CLogger::LEVEL_ERROR, 'toxus.StreamAction');      
      throw new CHttpException(505, 'Can not open file '.$filename);
//      header("HTTP/1.1 505 Internal server error");
//      return;
    }

    $begin	= 0;
    $end	= $size - 1;

    if (isset($_SERVER['HTTP_RANGE'])) {
      if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
        $begin	= intval($matches[1]);
        if (!empty($matches[2])) {
          $end	= intval($matches[2]);
        }
      }
    }

    if (isset($_SERVER['HTTP_RANGE'])){
      header('HTTP/1.1 206 Partial Content');
    } else {
      header('HTTP/1.1 200 OK');
    }
    $mimeType = $fileInfo->contentType;
    header("Content-Type: $mimeType"); 
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: no-cache');  
    header('Accept-Ranges: bytes');
    header('Content-Length:' . (($end - $begin) + 1));
    if (isset($_SERVER['HTTP_RANGE'])){
      header("Content-Range: bytes $begin-$end/$size");
    }
    header("Content-Disposition: inline; filename=".($this->userFilename ? $this->userFilename :$filename));
    header("Content-Transfer-Encoding: binary");
    header("Last-Modified: $time");

    $cur	= $begin;
    fseek($fm, $begin, 0);

    while(!feof($fm) && $cur <= $end && (connection_status() == 0)) {
      print fread($fm, min(1024 * 16, ($end - $cur) + 1));
      $cur += 1024 * 16;
    }
    @fclose($fm);
  }  
}
