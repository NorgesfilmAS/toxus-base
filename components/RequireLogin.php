<?php
/**
 * Behavior that makes every page secure.
 * By using allowedUrl pages can be excluded
 * 
 * version 1.0 jvk
 */
class RequireLogin extends CBehavior
{
	protected $_allowedSystemUrl = array(
			'site/error',
			'site/login',
			'site/passwordRequest',
	);
	/**
	 * the list of url that do not require a login
	 * 
	 * @var array
	 */
	public $allowedUrl = array();
	
	public function attach($owner)
	{
    $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
	}
	
	public function handleBeginRequest($event)
	{
		$page = $_SERVER['REQUEST_URI'];
		$dir = Yii::app()->baseUrl;
		$page = substr($page, strlen($dir) +1);
		Yii::log('Login for '.$page, CLogger::LEVEL_INFO, 'toxus.compontents.RequireLogin');
		if (Yii::app()->urlManager->showScriptName) {
			$part = array('');
			// page = index.php?r=site/search&XDEBUG_SESSION_START=netbeans-xdebug
			//   or
			// page = index.php/site/login
			
			$phpFile = explode('?', $page);
			if (count($phpFile) == 1) {
				$phpFile = explode('/', $page, 2);
			}
			if (count($phpFile) > 1) {
				$args = explode('&', $phpFile[1]);
				$l = 0;				
				while ($l < count($args)) {
					if (substr($args[$l], 0,2) == 'r=') {
						$part = explode('/', substr($args[$l],2));
						$page = count($part) > 1 ? ($part[0].'/'.$part[1]) : $part[0];
						break;
					}	elseif ( strstr($args[$l], '=') == false) {
						$part = explode('/', $args[$l]);
						$page = count($part) > 1 ? ($part[0].'/'.$part[1]) : $part[0];
						break;
					}
					$l++;
				}
			}
			Yii::log('Script visible', CLogger::LEVEL_INFO, 'toxus.compontents.RequireLogin');
		} else {
			$part = explode('/', $page);
			Yii::log('Script invisible', CLogger::LEVEL_INFO, 'toxus.compontents.RequireLogin');
		}
		if ($part[0] != 'gii' && Yii::app()->user->isGuest && !in_array($page, $this->allowedUrl + $this->_allowedSystemUrl)) {
			Yii::log('Login required', CLogger::LEVEL_INFO, 'toxus.compontents.RequireLogin');
      Yii::app()->user->loginRequired();
    } else {
			Yii::log('No login required', CLogger::LEVEL_INFO, 'toxus.compontents.RequireLogin');
		}
	}
}
