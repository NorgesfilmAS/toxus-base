<?php

class ClientConfigBase extends CComponent
{
	public function init()
	{
	}
	
	/**
	 * returns the absolute path to the file. If the customer file exist this
	 * path is returned otherwise the default paths is used
	 * 
	 * @param string $filename
	 * @param boolean $relative if true the path is relative the the protected directory
	 * @return string
	 */
	public function fileReadName($filename, $relative = false)
	{
		$path = Yii::getPathOfAlias('customer');
		if (file_exists($path.'/'.$filename)) {
			$file = $path.'/'.$filename;
		} else {
			$file = Yii::getPathOfAlias('webroot.customers.default').'/'.$filename;
		}
		if ($file && $relative) {
			$file = '/..'.substr($file, strlen(Yii::getPathOfAlias('webroot')));
		}
		return $file;
	}
	/**
	 * return the absolute path the the customer file
	 * 
	 * @param string $filename
	 * @return string
	 */
	public function fileWriteName($filename)
	{
		return Yii::getPathOfAlias('customer').'/'.$filename;
	}
	/**
	 * checks if the user has a custom file set
	 * 
	 * @param string $filename
	 * @return boolean
	 */
	public function fileExists($filename)
	{
		$path = Yii::getPathOfAlias('customer');
		return file_exists($path.'/'.$filename);
	}
	
	/**
	 * Delete the customer file and 
	 * 
	 * @param string $filename
	 * @return boolean true if succeeded of the file is not found
	 */
	public function fileDelete($filename)
	{
		$name = $this->fileWriteName($filename);
		if (file_exists($name))
		  return unlink($filename);
		else
			return true;
	}
}