<?php

class BaseSiteController extends Controller
{
	public function actions()
  {
		return array(
			'error'	  => 'toxus.actions.ErrorAction',
			'login'   => array(
										'class' => 'toxus.actions.LoginAction',
										'loginUrl' => $this->createUrl('site/login'),
					),	
			'logout'	=> 'toxus.actions.LogoutAction',
			'passwordRequest' => 'toxus.actions.PasswordRequestAction',	
			'index2' => array(
							'class' => 'toxus.actions.ViewAction',	
							'view' => 'index',
							'defaultMode' => 'hidden',
					),
			'removeAssets' => 'toxus.actions.RemoveAssetsAction',
			'systemInfo' => array(
					'class'=> 'toxus.actions.SystemInfoAction',
					'onExtraInfo' => array($this, 'systemInfo'),
			),
			'system' => array(
					'class' => 'toxus.actions.ViewAction',	
					'view' => 'system',
					'form' => 'systemSetup',
			),	
			'index' => array(
					'class' => 'toxus.actions.ViewAction',	
					'view' => 'index',
			),
			'maintenance' => array(
					'class' => 'toxus.actions.ViewAction',	
					'view' => 'maintenance',
			)	
				
		);
	}	

	public function actionClearCache($id = null)
	{
		Yii::app()->imageCache->clear($id);
		Yii::app()->user->setFlash(Yii::t('app', 'Image cache has been cleared'));
		$this->redirect($this->createUrl('site/search'));
	}

	public function actionDecode($filename)
	{
		$name = YiiBase::getPathOfAlias('webroot').'/'.$filename;
		if (Util::decodeUtf8File($name)) {
			echo 'Information is decoded';
		} else {
			echo 'Error: Information NOT decoded. Usage site/decode?filename=name where name is '.$name;
		}	
		Yii::app()->end();
	}
	public function actionEncode($filename)
	{
		$name = YiiBase::getPathOfAlias('webroot').'/'.$filename;
		if (Util::encodeUtf8File($name)) {
			echo 'Information is encoded';
		} else {
			echo 'Error: Information NOT decoded. Usage site/encode?filename=name where name is '.$name;
		}	
		Yii::app()->end();
	}
	
}