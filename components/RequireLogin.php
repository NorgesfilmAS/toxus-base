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
		Yii::log('Testing login for '.$page, CLogger::LEVEL_INFO, 'security.toxus.compontents.RequireLogin');
		if (Yii::app()->urlManager->showScriptName) {
			$parts = array('');
			// page = index.php?r=site/search&XDEBUG_SESSION_START=netbeans-xdebug
			//   or
			// page = index.php/site/login?r=laksdfjla
			$php = substr($page, strlen('index.php') + 1); // makes it: r=site/search or site/login?r=xxx
			if ($php === false || $php == '') {
				$page = 'site/index';
			} else {
				$parts = explode('?', $php);
				if (count($parts) > 0 && strlen($parts[0]) == 0) {  // it's ?r=index.php
					$parts = explode('=', $parts[0]);
					if (count($parts) > 1) {
						$page = $parts[1];			// the site/login
					} else {
						Yii::log('Failed scanning url: '.$page, CLogger::LEVEL_ERROR, 'security.toxus.compontents.RequireLogin');
					}
				} else {				// it's site/login?xx=lasd
					$page = $parts[0];
				}
			}	
			Yii::log('index.php is visible: page changed to '.$page, CLogger::LEVEL_INFO, 'security.toxus.compontents.RequireLogin');
		} else {
			$parts = explode('/', $page);
			Yii::log('Direct url', CLogger::LEVEL_INFO, 'security.toxus.compontents.RequireLogin');
		}
		$a = array_merge($this->allowedUrl, $this->_allowedSystemUrl);
		foreach ($a as $path) {
			if (fnmatch($path, $page)) {
				Yii::log('No login required', CLogger::LEVEL_INFO, 'security.toxus.compontents.RequireLogin');
				return;
			}
		}
		Yii::log('Login required for '.$page, CLogger::LEVEL_INFO, 'security.toxus.compontents.RequireLogin');
		if (!Yii::app()->user->isGuest) {
			return;
		}
		Yii::app()->user->loginRequired();
	}
}
