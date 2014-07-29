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
	 * 
	 * 
	 * @param string $name the name of the file
	 * @throws CHttpException
	 */
	public function run($name='')
	{
		$this->checkRights();
		if ($name == '') {
			if (count($_GET) == 0) {
				throw new CHttpException(404, 'File not found');
			}
			$name = reset($_GET);
			if ($name == '') {
				$name = key($_GET);
			}
		}
		if (substr($this->path,0,1) == '@') {
			$filename = YiiBase::getPathOfAlias(substr($this->path,1)).'/'.$name;
		} else {	
			$filename = $this->path.$name;
		}	
		$ff = new FileInformation($filename);
		if (!$ff->exists()) {
			throw new CHttpException(404, 'File not found');
		}
		if ($this->userFilename === false) {
			$this->userFilename = $ff->filename;
		}
		if ($this->forceDownload) {
			header('Content-disposition: attachment; filename='.$this->userFilename);
		}	
		header('Content-type: '.$ff->contentType);
		set_time_limit(0);
		$file = @fopen($ff->path, "rb");
		try {
			while(!feof($file))	{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}		
		} catch (Exception $e) {
		}
		@fclose($file);		
	}
}