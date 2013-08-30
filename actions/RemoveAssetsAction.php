<?php
/**
 * action to clear the assets directory
 */
class RemoveAssetsAction extends CAction
{
	private function rmDirRec($dir)
	{
		$objs = glob($dir."/*");
		if ($objs){
			foreach($objs as $obj) {
				is_dir($obj) ? $this->rmDirRec($obj) : unlink($obj);
			}
		}
		rmdir($dir);
	}

	
	public function run($clearCache = false)
	{
		defined('DS') or define('DS',DIRECTORY_SEPARATOR);		
		
		$AM  = new CAssetManager;
		$dir = $AM->getBasePath();
		if(file_exists($dir)) {
			$files = glob($dir.DS."*");
			foreach($files as $del) {
				$s = pathinfo($del, PATHINFO_BASENAME);
				if(is_dir($del) && ($clearCache || !($s == 'cache'))) {
					$this->rmDirRec($del);
				} elseif (($s != 'cache')) {
					unlink($del);
				}
			}
			echo 'all files have been removed';
		} else {
			echo "Assets directory not exists";
		}
	}
}