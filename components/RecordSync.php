<?php
/**
 * checks for changes in a record.
 * generates a unique string and stores it in the record so changes can be
 * found in complex structures
 * 
 * use the run($records) to run the process on a selected batch of records
 * define what fields are checked for changes by setting the $attributes array
 * the $storeAttribute is used to store the temp information
 * 
 * to process none straight field information overload:
 *   * record2Array : convert the record in an array structure
 * 
 * records are marked by the $markAttribute (0|1).
 * Direct processing is possible by setting the $onFieldChanged function. The $markAttribute
 * will than not be used
 * 
 * 
 * version 1.0 JvK 2014.08.05
 */

class RecordSync extends CComponent
{
	public $_errors = array();
	
	/**
	 * the array of fields to export
	 * 
	 * @var array|false
	 */
	public $attributes = false; 
	
	/**
	 * the attribute in the record holding the stored information
	 */
	public $storeAttribute = false;
	
	/**
	 * the field set to 1 if the record is changed
	 * @var string
	 */
	public $markAttribute= false;
	/**
	 *  function($record, $data);
	 * 
	 * @var function param 
	 */
	public $onFieldChanged = false;
	
	private $_state = false;
	
  public $echoMessage = false;
  
	public function __construct() {
		$this->init();
	}
	public function init()
	{
		
	}
  
  public function echoText($text) {
    if ($this->echoMessage) {
      echo 'msg: '.$text."\n";
    }
  }
	/**
	 * convert an record to an array in a standard way
	 * should be overloaded to create a special sync
	 * 
	 * Error can be stored by calling addError(err,id,msg);
	 * 
	 * @param CActiveRecord $record
	 * @return array structure to save
	 */
	protected function record2Array($record)
	{
		$result = array();
		foreach ($record->attributes as $attribute) {
			if ($this->attributes === false || in_array($attribute, $this->attributes)) {
			  $result[$attribute] = $record->$attribute;
      }
		}
		return $result;
	}
	

	/**
	 * runs through the active records marking the records that are
	 * changed since the last run
   * 
   * only calls the mailchimp interface if something has changed in the database
   * 
	 * 
	 * @param array of CActiveRecord $records
	 */
	public function run($records)
	{
		if ($this->storeAttribute === false) {
			throw new CDbException(Yii::t('base', 'The storeAttribute should be set'));
		}
		if ($this->markAttribute === false && $this->onFieldChanged === false) {
			throw new CDbException(Yii::t('base', 'The mark field or onFieldChanged should be set'));
		}
		$this->clearErrors();
		$store = $this->storeAttribute;
		$mark = $this->markAttribute;
		foreach ($records as $record) {
      $id = $record->id;
			$data = $this->record2Array($record);
			if ($data !== false) {
				$dataJson = CJSON::encode($data);
        // check that something has changed
				if ($record->$store != $dataJson) {
					if ($this->onFieldChanged) {
						if (!call_user_func($this->onFieldChanged, $record, $data)) {
							foreach ($this->error as $err) {
								$this->errors[] = $err;
							}							
							continue;
						}						
					} else {					
						if (empty($record->$mark)) {
							$record->$mark = 1;						
						}					
					}
					$record->$store = $dataJson;
					$record->save();
				} elseif (!empty($record->$mark)) {
					$record->$mark = 0;
					$record->save();
				}		
			}	
		}
		return count($this->errors) == 0;
	}
	
	/**
	 * should be overload
	 */
	public function runAll()
	{
		throw new CDbException('runAll should be overloaded to use');
	}

	
	public function getErrors()
	{
		return $this->_errors;
	}
	public function clearErrors()
	{
		$this->_errors = array();
	}
	public function addError($err, $id, $msg, $e)
	{
		$this->_errors[] = array(
			'code' => $err,
			'id' => $id,
			'message' => $msg,
      'error' => get_class($e)
		);
	}
	
	/**
	 * return the state record of this process
	 * 
	 * @return ProcessState
	 */
	public function getState() 
	{
		if ($this->_state == false) {
			$this->_state = new ProcessState();
		}
		return $this->_state;
	}
	
	
}

