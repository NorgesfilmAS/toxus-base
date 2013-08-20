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
		
		$part = explode('/', $page);
		if ($part[0] != 'gii' && Yii::app()->user->isGuest && !in_array($page, $this->allowedPages)) {
       Yii::app()->user->loginRequired();
    }
	}
}
