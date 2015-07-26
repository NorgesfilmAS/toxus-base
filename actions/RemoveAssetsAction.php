<?php
/**
 * action to clear the assets directory
 */
class RemoveAssetsAction extends CAction
{
	private $_debug;
	
	private function rmDirRec($dir)
	{
		$objs = glob($dir."/*");
		if ($objs){
			foreach($objs as $obj) {
				if (is_dir($obj)) {
					$this->rmDirRec($obj);
				} else {	
					if ($this->_debug) {
						echo 'file: '.$obj.'<br />';
					} else {
						unlink($obj);
					}
				}
			}
		}
		if ($this->_debug) {
			echo 'directory: '.$dir.'<br />';			
		} else {
			rmdir($dir);
		}	
	}

	
	public function run($clearCache = 0, $debug = 0)
	{
		defined('DS') or define('DS',DIRECTORY_SEPARATOR);		
		$this->_debug = $debug;
		$AM  = new CAssetManager;
		if ($this->_debug) {
			echo 'basePath: '.$AM->getBasePath().'<br />';
		}
		$dir = $AM->getBasePath();
		if(file_exists($dir)) {
			$files = glob($dir.DS."*");
			foreach($files as $del) {
				$s = pathinfo($del, PATHINFO_BASENAME);
				if(is_dir($del) && ($clearCache || !($s == 'cache'))) {
					$this->rmDirRec($del);
				} elseif (($s != 'cache')) {
					if ($this->_debug) {
						echo 'file: '.$del.'<br />';
					} else {
						unlink($del);
					}	
				}
			}
			echo 'all files have been removed';
		} else {
			echo "Assets directory not exists";
		}
	}
}