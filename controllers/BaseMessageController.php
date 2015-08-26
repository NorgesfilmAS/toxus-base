<?php
/**
 * the contact the admin system
 * needs: MessageModel => Message
 * migration: m130405_093547_message_system
 */


class BaseMessageController extends Controller
{
	public function actionIndex()
	{
		$this->model = new Message();
		if (isset($_POST['Message'])) {
			$this->model->attributes = $_POST['Message'];
			if ($this->model->validate()) {
				if ($this->model->save()) {
					$this->redirect($this->createUrl('message/thanks'));
				}	
			}
			//Yii::app()->user->setFlash('error', var_export($this->model->getErrors(), true));
			foreach ($this->model->getErrors() as $attribute => $msgs)
				foreach ($msgs as $msg)
					$s .= $msg."\n";
			Yii::app()->user->setFlash('error',$s);
		}		
		$this->render('index', array('model' => $this->model));
	}
	
	public function actionThanks()
	{
		$this->render('thanks');
	}
	
	public function actionList()
	{
		$model = new Message();
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Message']))
			$model->attributes=$_GET['Message'];
		
		$this->render('list', array('model' => $model));
	}
	
	public function actionReply($id)
	{
		$model = Message::model()->findByPk($id);
		if ($model == null)
			throw new CDbException('Message not found');
		if (isset($model->previous)) {
			$this->model = $model;
			$this->render('replyView', array('model' => $this->model));
		} else {
			$this->model = new Message();
			$this->model->reply_to_message_id = $id; 
			// $this->loadModel($id, 'Message');					
			$this->model->email = Yii::app()->params['editor-email'];
			$this->model->name = Yii::app()->params['editor'];
			if (isset($_POST['Message'])) {
				$this->model->attributes = $_POST['Message'];
				if ($this->model->validate()) {
					if ($this->model->save()) {
						$this->redirect($this->createUrl('message/list'));
					}
				}
			}
			$form = require(Yiibase::getPathOfAlias("application.views.message.form").'.php');		
			$this->render('reply', array('model' => $this->model, 'form' => $form));
		}	
	}
}