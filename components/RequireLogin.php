<?php
class RequireLogin extends CBehavior
{
	public $allowedPages = array(
			'site/error',
			'site/login',
			'site/passwordRequest',
	);
	
	public function attach($owner)
	{
    $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
	}
	
	public function handleBeginRequest($event)
	{
		$page = $_SERVER['REQUEST_URI'];
		$dir = Yii::app()->baseUrl;
		$page = substr($page, strlen($dir) +1);

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
		} else {
			$part = explode('/', $page);
		}
		if ($part[0] != 'gii' && Yii::app()->user->isGuest && !in_array($page, $this->allowedPages)) {
       Yii::app()->user->loginRequired();
    }
	}
}
