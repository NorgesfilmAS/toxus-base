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
		);
	}	

	public function actionClearCache($id = null)
	{
		Yii::app()->imageCache->clear($id);
		Yii::app()->user->setFlash(Yii::t('app', 'Image cache has been cleared'));
		$this->redirect($this->createUrl('site/search'));
	}

	
}