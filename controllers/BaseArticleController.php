<?php

class BaseArticleController extends Controller
{
	public function actions()
	{
		return array(
			'create' => array(
				'class' => 'toxus.actions.CreateAction',
				'form' => 'newArticle',
				'modelClass' => 'ArticleModel',	
				'successUrl' => 'article/index',	
			)	,
			'edit' => array(
				'class' => 'toxus.actions.UpdateAction',
				'form' => 'changeArticle',
				'modelClass' => 'ArticleModel',				
				'view' => 'viewForm',	
        'mode' => 'edit',
				'successUrl' => 'article/index',	
			),
			'v' => array(
				'class' => 'toxus.actions.ViewAction',
				'view' => 'view',
				'onCreateModel' => array($this, 'viewModel'),	
			),	
		);
	}
	public function actionIndex()
	{
		if (isset($_GET['id'])) {
			$this->model = ArticleModel::model()->findByPk($_GET['id']);
		} elseif (count($_GET) == 1) {
			$this->model = ArticleModel::model()->findByKey(key($_GET));
		}	
    $params = array(
      'form' => $this->loadForm('form'),
      'mode' => 'view'
    );
		$this->render('index', $params);
	}
	
	public function viewModel()
	{
		// must get the v/xx from the url
		$route = $this->actionParams;
		$this->model = ArticleModel::model()->findByKey($route['key']);
	}
	
	public function actionList()
	{
		$search = isset($_GET['q']) ? $_GET['q'] : false;
		$this->model = ArticleModel::model()->findAll($search);
		return $this->render('list');
	}
}