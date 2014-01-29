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
	 * sends a file to the user
	 * 
	 * @param string $name the name of the file in the path directory
	 * @throws CHttpException 404
	 */
	public function run($name='')
	{
		$this->checkRights();
		$filename = $this->path.$name;
		$ff = new FileInformation($filename);
		if (!$ff->exists()) {
			throw new CHttpException(404, 'File not found');
		}
		if ($this->forceDownload) {
			header('Content-disposition: attachment; filename='.$ff->filename);
		}	
		header('Content-type: '.$ff->contentType);
		set_time_limit(0);
		$file = @fopen($ff->path,"rb");
		while(!feof($file))	{
			@print(@fread($file, 1024*8));
			@ob_flush();
			@flush();
		}		
		@fclose($file);		
		Yii::app()->end(200);
	}
}