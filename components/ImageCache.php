<?php

/**
 * 
 */
class ImageCache extends CComponent
{
	const BASEPATH = 'webroot.assets.cache';
	const ROOTCACHE = '/assets/cache/';
	const DIR_ORIGINAL = 'original';
	
	public $sizes = array(
			self::DIR_ORIGINAL => array(
				'width' => null,
				'height' => null,	
			),
			'small' => array(
				'width' => 80,
				'height' => 80,
				'quality' => 90,	
			),
			'medium' => array(
				'width' => 200,
				'height' => 200,	
				'quality' => 75,					
			),
			'large' => array(
				'width' => 400,
				'height' => 300,	
				'quality' => 80,					
			),
			'wide' => array(
				'width' => 400,
				'height' => 200,	
				'quality' => 90,															
			)
	);				

	/**
	 * check that all needed directories exist
	 */
	public function validateSizeDirectories()
	{
		foreach ($this->sizes as $name => $size) {
			$path = Yii::getPathOfAlias(self::BASEPATH.'.'.$name);
			if (!is_dir($path)) {
				mkdir($path);
			}	
		}
	}
	
	public function init()
	{
		$path = Yii::getPathOfAlias(self::BASEPATH);
		if (!is_dir($path)) {
			mkdir($path);
		}
		$this->validateSizeDirectories();
	}
	/**
	 * returns the fysical path to the 'original' directory
	 */
	public function getPath()
	{
		return Yii::getPathOfAlias(self::BASEPATH.'.'.self::DIR_ORIGINAL).'/';
	}
	/**
	 * remove an image from the cache
	 *
	 * @param array $options 
	 * @param string $name the name of the file to remove 
	 */
	public function clear($id = null, $options=array())
	{
		$defaults = array_merge(array(
				'deleteOriginal' => true,				
			),
			$options			
		);
		if ($id == null) { // remove everything from the cache
			// http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
			foreach ($this->sizes as $size => $options) {
				if ($defaults['deleteOriginal'] == true || $size != self::DIR_ORIGINAL) {
					foreach (new DirectoryIterator(Yii::getPathOfAlias(self::BASEPATH.'/'.$size)) as $fileInfo)
						if(!$fileInfo->isDot())
							unlink($fileInfo->getPathname());
				}
			}	
		} else {
			foreach ($this->sizes as $size => $options) {
				if ($defaults['deleteOriginal'] == true || $size != self::DIR_ORIGINAL) {
					$path = Yii::getPathOfAlias(self::BASEPATH.'/'.$size.'/'.$id);
					if (file_exists($path))
					  unlink($path);
				}	
			}
		}	
	}
	
	/**
	 * Retrieves the Url of the file to shou
	 * 
	 * @param string $name name of the image without any path
	 * @param string $size one of the size definitions
	 * @returns string the url to the file or false if file not found
	 * @throws CException when the size is not allowed
	 */
	public function imageUrl($name, $size = 'original')
	{
		if (!isset($this->sizes[$size])) {
			throw new CException('The size "'.$size.'" for the images is unknown');
		}
		$pathFile = Yii::getPathOfAlias(self::BASEPATH.'.'.$size).'/'.$name;
		if (!file_exists($pathFile)) {
			$pathOriginal = $this->path.$name;
			if (!file_exists($pathOriginal)) {
				return false;	// file not found
			}
			if (!$this->imageResize($pathOriginal, $pathFile, $this->sizes[$size])) {
				// image is corrupted
				return false;
			}
		}	
		return Yii::app()->baseUrl.self::ROOTCACHE.$size.'/'.$name;
	}
	
	/**
	 * http://salman-w.blogspot.nl/2008/10/resize-images-using-phpgd-library.html
	 * 
	 * @param type $originalFilename the name in the org directory
	 * @param type $newFilename the filename to create
	 * @param type $size the array with (width, height)
	 */
	/**
	define('THUMBNAIL_IMAGE_MAX_WIDTH', 150);
	define('THUMBNAIL_IMAGE_MAX_HEIGHT', 150);

	function generate_image_thumbnail($source_image_path, $thumbnail_image_path)
	{

	 */
	private function imageResize($originalFilename, $newFilename, $size)
	{
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($originalFilename);
    switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$source_gd_image = imagecreatefromgif($originalFilename);
				break;
			case IMAGETYPE_JPEG:
				$source_gd_image = imagecreatefromjpeg($originalFilename);
				break;
			case IMAGETYPE_PNG:
				$source_gd_image = imagecreatefrompng($originalFilename);
				break;
    }
    if ($source_gd_image === false) {
			return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = $size['width'] / $size['height'];
    if ($source_image_width <= $size['width'] && $source_image_height <= $size['height']) {
			$thumbnail_image_width = $source_image_width;
			$thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
			$thumbnail_image_width = (int) ($size['height'] * $source_aspect_ratio);
			$thumbnail_image_height = $size['height'];
    } else {
			$thumbnail_image_width = $size['width'];
			$thumbnail_image_height = (int) ($size['width'] / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				imagegif($thumbnail_gd_image, $newFilename);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($thumbnail_gd_image, $newFilename, isset($size['quality']) ? $size['quality'] : 90);
				break;
			case IMAGETYPE_PNG:
				imagepng($thumbnail_gd_image, $newFilename, isset($size['quality']) ? $size['quality'] : 90);
				break;			
		}
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;		
	}
	
}