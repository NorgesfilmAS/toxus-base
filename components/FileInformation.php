<?php
/**
 * File information routines
 * 
 * version 1.0 jvk 2013-11-19
 * /dir/name.ext
 * 
 * - filename			
 * - dirName			(/dir)
 * - basename			(name.ext)
 * - extension		(ext)
 * - name					(name)	
 * - exists
 * - size
 * - sizeText
 * - time
 * 
 * - calcMd5()
 * 
 * modification on the file:
 *  - touch()
 */

class FileInformation extends CComponent
{
	private $_path = null;
	private $_parts = false;
	
	public function __construct($filename = null) {
		$this->_path = $filename;
		$this->init();
	}
	
	public function init()
	{
		if ($this->exists()) {
			$this->_path = realpath($this->_path);
		}	
		$this->_parts = pathinfo($this->_path);		
	}
	
	public function setPath($filename)
	{
		$this->_path = $filename;
		$this->init();
	}
	public function getPath()
	{
		return $this->_path;
	}
	
	public function exists()
	{
		return ($this->_path != null) && file_exists($this->_path);
	}
	public function getDirName()
	{
		return $this->_parts == false ? null : $this->_parts['dirname'];
	}
	public function getbaseName()
	{
		return $this->_parts == false ? null : $this->_parts['basename'];
	}
	public function getExtension()
	{
		return $this->_parts == false? null : $this->_parts['extension'];
	}
	public function getName()
	{
		return $this->_parts == false ? null : $this->_parts['filename'];
	}
	public function getFilename()
	{
		return $this->_parts == false ? null : ($this->extension == null ? $this->name : ($this->name.'.'.$this->extension));
	}
	
	public function getSize()
	{
		return $this->exists() == false ?  0 : filesize($this->_path);
	}
	
  public function getSizeText()
	{
		if ($this->exists() == false) {
			return null;
		} else {
			$bytes = sprintf('%u', $this->size);

			if ($bytes > 0)	{
				$unit = intval(log($bytes, 1024));
				$units = array('B', 'KB', 'MB', 'GB', 'TB');

				if (array_key_exists($unit, $units) === true)	{
					return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
				}
			}
			return $bytes.' B';
		}	
	}
	
	/**
	 * returns the timestamp
	 */
	public function getTime()
	{
		return $this->exists() == false ? 0 : filectime($this->_path);
	}
  
  public function getDate() {
    return date('r', filemtime($this->_path));
  }
	
	public function getContentType()
	{
		if ($this->exists() == false)
			return false;
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $this->path);
		finfo_close($finfo);		
		return $type;
	}
					
	/**
	 * set the modification date to now
	 */
	public function touch()
	{
		return $this->exists() == false ? false : touch($this->_path);
	}
	
	/**
	 * calculate the md5 of the file
	 * @return md5 string or false if error occured
	 */
	public function calcMd5() 
	{
		try {
			return md5_file($this->_path);			
		} catch (Exception $ex) {
			Yii::log('Error calculating md5 on '.$this->_path, CLogger::LEVEL_ERROR, 'toxus.fileInformation');
			return false;
		}
	}
}