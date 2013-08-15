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
			'thumb' => array(
				'width' => 300,
				'height' => 200,	
				'quality' => 75,					
				'fill' => true,		// make the image EXACT this size, padding it
				'background-color' => array(230,230,235),	
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
	 *      deleteOriginal true/false if true the original directory is also emptied
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
	 * Retrieves the Url of the file to show
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
		$basename = null;
		if (substr($name, 0,1) == '/') { // it the fysical path to the root
			$basename = pathinfo($name, PATHINFO_BASENAME);
			$pathFile = Yii::getPathOfAlias(self::BASEPATH.'.'.$size).'/'.$basename;
		} else {
			$pathFile = Yii::getPathOfAlias(self::BASEPATH.'.'.$size).'/'.$name;
		}	
		if (!file_exists($pathFile)) {
			if ($basename) {
				$pathOriginal = $name;
			} else {
				$pathOriginal = $this->path.$name;
			}	
			if (!file_exists($pathOriginal)) {
				return false;	// file not found
			}
			if (isset($this->sizes[$size]['fill'])) {
				if (!$this->imageThumb($pathOriginal, $pathFile, $this->sizes[$size])) {
					// image is corrupted
					return false;					
				}
			} else {	
				if (!$this->imageResize($pathOriginal, $pathFile, $this->sizes[$size])) {
					// image is corrupted
					return false;
				}
			}	
		}	
		if ($basename) {
			return Yii::app()->baseUrl.self::ROOTCACHE.$size.'/'.$basename;
		}
		return Yii::app()->baseUrl.self::ROOTCACHE.$size.'/'.$name;
	}
	
	/**
	 * load the binary data
	 */
	private function openImage($name)
	{
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($name);
    switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$source_gd_image = imagecreatefromgif($name);
				break;
			case IMAGETYPE_JPEG:
				$source_gd_image = imagecreatefromjpeg($name);
				break;
			case IMAGETYPE_PNG:
				$source_gd_image = imagecreatefrompng($name);
				break;
			case IMAGETYPE_BMP :
				$source_gd_image = imagecreatefromwbmp($name);
				break;
    }
    if ($source_gd_image === false) {
			return false;
    }
		return $source_gd_image;
	}
	
	/**
	 * save the image to disk
	 * 
	 */	
	private function saveImage($type, $name, $data, $quality = 90)
	{
		switch ($type) {
			case IMAGETYPE_GIF:
				imagegif($data, $name);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($data, $name, $quality);
				break;
			case IMAGETYPE_PNG:
				imagepng($data, $name, $quality);
				break;			
			default :
				return false;
		}
		return true;
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
	public function imageResize($originalFilename, $newFilename, $size)
	{
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($originalFilename);
		$source_gd_image = $this->openImage($originalFilename);
		
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
		
		$this->saveImage($source_image_type, $newFilename, $data, isset($size['quality']) ? $size['quality'] : 90);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;		
	}
	
	
	/**
	 * from: http://stackoverflow.com/questions/747101/resize-crop-pad-a-picture-to-a-fixed-size
	 * 
	 * @param type $img		the binary data of the image
	 * @param type $box_w width in pixels
	 * @param type $box_h height in pixels
	 * @param type $backgroundColor an array of rgb values for the background color
	 * @return image raw data of the image
	 */
	// private function thumbnailBox($img, $box_w, $box_h, $backgroundColor = array(200,200,205)) {
	private function imageThumb($originalFilename, $newFilename, $sizeOptions) // $fli$img, $box_w, $box_h, $backgroundColor = array(200,200,205)) {
	{
		$size = array_merge(
						array(
							'width' => 200,
							'height' => 200,
							'quality' => 90,	
							'fill' => true,
							'background-color' => array(200,200,205),	
						),
						$sizeOptions);			

		$img = $this->openImage($originalFilename);
    //create the image, of the required size
    $new = imagecreatetruecolor($size['width'], $size['height']);
    if($new === false) {
        //creation failed -- probably not enough memory
      return false;
    }

    //Fill the image with a light grey color
    //(this will be visible in the padding around the image,
    //if the aspect ratios of the image and the thumbnail do not match)
    //Replace this with any color you want, or comment it out for black.
    //I used grey for testing =)
    $fill = imagecolorallocate($new, $size['background-color'][0], $size['background-color'][1], $size['background-color'][2]);
    imagefill($new, 0, 0, $fill);

    //compute resize ratio
    $hratio = $size['height'] / imagesy($img);
    $wratio = $size['width'] / imagesx($img);
    $ratio = min($hratio, $wratio);

    //if the source is smaller than the thumbnail size, 
    //don't resize -- add a margin instead
    //(that is, dont magnify images)
    if($ratio > 1.0)
        $ratio = 1.0;

    //compute sizes
    $sy = floor(imagesy($img) * $ratio);
    $sx = floor(imagesx($img) * $ratio);

    //compute margins
    //Using these margins centers the image in the thumbnail.
    //If you always want the image to the top left, 
    //set both of these to 0
    $m_y = floor(($size['height'] - $sy) / 2);
    $m_x = floor(($size['width'] - $sx) / 2);

    //Copy the image data, and resample
    //
    //If you want a fast and ugly thumbnail,
    //replace imagecopyresampled with imagecopyresized
    if(!imagecopyresampled($new, $img,
				    $m_x, $m_y, //dest x, y (margins)
						0, 0, //src x, y (0,0 means top left)
						$sx, $sy,//dest w, h (resample to this size (computed above)
						imagesx($img), imagesy($img)) //src w, h (the full size of the original)
				) {
			//copy failed
			imagedestroy($new);
			return false;
    }
		list($sourceImageWidth, $sourceImageHeight, $sourceImageType) = getimagesize($originalFilename);
		$this->saveImage($sourceImageType, $newFilename, $new, $size['quality']);
		
		imagedestroy($new);			
    //copy successful
    return true;
	}	
	
	
}