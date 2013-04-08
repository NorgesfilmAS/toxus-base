<?php

/**
 * should be activate in the main.php / components as 
 * 	'pageLog' => array(
 *		'class' => 'PageLog',
 *	),	
 */
class PageLog extends CComponent
{
	private $_logging;
	
	public function init()
	{		
	}

	public function getLog()
	{
		if (empty($this->_logging)) {
			$this->_logging = new Logging();
		}	
		return $this->_logging;
	}

	public function addText($msg)
	{
		$this->log->message .= $msg."\n";		
	}
	
	public function add($data)
	{
		$this->log->message .= var_export($data, true)."\n";
	}
	
	public function writeExecutionTime($writeTime = true, $controller = null)
  {
		if ($writeTime) {
		  $this->log->processing_time = Yii::getLogger()->getExecutionTime();
		}	
		$this->_logging->profile_id = Yii::app()->user->id;
		if ($controller == null)
			$controller = Yii::app ()->controller;
		if ($controller) {			
			$this->_logging->controller = get_class($controller);
			if ($controller instanceof Controller) {
				if (isset($controller->model)) {
					$this->_logging->model_id = $controller->model->id;
				}	
			} else {
				$this->addText('Controller is not a Controller');
			}
		} else {
			$this->addText('There is no controller');
		}	
		$this->log->save();
	}					
					
}
