<?php

/**
 * should be activate in the main.php / components as 
 * 	'pageLog' => array(
 *		'class' => 'PageLog',
 *	),	
 */
class PageLog extends CComponent
{
	/**
	 * set this to name of the db (dbSystem) so the logging is written to an external db
	 * 
	 * @var string
	 */
	public $dbName;
	public $reportErrors = false;
	
	/**
	 *
	 * @var string the model to use for logging
	 */
	public $loggingModel = 'LoggingModel';
	
	/**
	 *
	 * @var boolean. save after every modification
	 */
	public $autoSave = false;
	
	private $_logging;
	
	public function init()
	{		
	}

	public function getLog()
	{
		if (empty($this->_logging)) {
			$model = $this->loggingModel;			
			if ($this->dbName) {
				$name = $this->dbName;
				$this->_logging = new $model(Yii::app()->$name);
			} else {
				$this->_logging = new $model();
			}	
		}	
		return $this->_logging;
	}

	public function addText($msg)
	{
		$this->log->message .= $msg."\n";		
		if ($this->autoSave)$this->log->save();
	}
	
	public function add($data)
	{
		$this->log->message .= var_export($data, true)."\n";
		if ($this->autoSave)$this->log->save();		
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
				if (isset($controller->model) && isset($controller->model->id)) {
					$this->_logging->model_id = $controller->model->id;
				}	
			} else {
				$this->addText('controller is not an instance Controller but of '.  get_class($controller));
			}
		} else {
			$this->addText('There is no controller');
		}	
		if (!$this->log->save() && $this->reportErrors)
			throw new CDbException('Could not write to log');
	}					
					
}
