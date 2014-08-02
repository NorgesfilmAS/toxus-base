<?php

/**
 * shows an article and leaves the menu as standard
 * 
 */
Yii::import('toxus.actions.BaseAction');

class ArticleReadAction extends BaseAction
{
	
	public function run($key)
	{
		$params = array(
			'allowEdit' => false,	
			'menuItem' => $this->menuItem,	
		);
		
		$a = new ArticleModel();
		$this->controller->model = $a->findByKey($key);		
		if ($this->controller->model) {
			
		} else {
			$params['error'] = Yii::t('base', 'Article {key} not found.',array('{key}' => $key));
		}
		$this->controller->render('viewHelp', $this->mergeParams($params));
	}
	
}