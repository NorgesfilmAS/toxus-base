<?php

class CreatePopupAction extends CAction
{
	public $modelClass = null;
	public $form = null;
	
	public function run()
	{
		$model = $this->modelClass;
		if ($model == null)			throw new CHttpException('expecting modelClass');
		$this->controller->model = new $model;
		if (isset($_POST[$model])) {
			$this->controller->model->attributes = $_POST[$model];
			if ($this->controller->model->save()) {
				Yii::app()->user->lastInsertId = $this->controller->model->id; // remember what we did add
				echo 'url';
				Yii::app()->end(200);// end our call
			}
		}
		
		Yii::import('toxus.actions.ViewAction');
		if ($form == null) {
			$this->form = lcfirst($model).'Fields';
		}
		$this->controller->render('create', array(
			'form' => $this->controller->loadForm($form), 
			'sub' => array(
					'isAjax' => true, 
					'refreshUrl'=> $this->controller->createUrl(lcfirst($model).'/view', array('id' => ViewAction::USE_LAST_ID)),
					'slaveFrame' => 'id-modal-body',
			)
		));
		
	}
}