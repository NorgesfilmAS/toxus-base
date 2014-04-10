<?php

class BaseJsonController extends Controller
{
	
	/**
	 * Allow the information to be send to other origins.
	 * 
	 * @var boolean
	 */
	protected $allowOtherOrigin = true;
	/**
	 * to definitions: 
	 *	- status: information about the call
	 *  - data:		the data returned to caller
	 * 
	 * @var array the result returned to the caller
	 */
	protected $result = array(
			'status' => array(
					'success' => true,					// if all went well
					'message' => '',						// message to the user
					'statuscode' => 200,				// http status codes
					'errors' => array(),				// a key => error text array
					'sessionId' => 'undefined',	//
			),
			'data' => array(),
	);
	
	public function init() {
		if ($this->allowOtherOrigin) {
			header('Access-Control-Allow-Origin: *');
		}	
		Yii::app()->json;				
		parent::init();
	}
	
	public function filters()
	{
		return array(
			'accessControl',
		);
  }
	public function accessRules()
  {
		return array(
				array('allow',
						'actions'=>array('login'),
						'roles'=>array('*'),
				),
				array('deny',
						'actions'=>array('*'),
						'users'=>array('*'),
				),
		);
  }	
	
	public function getSuccess()
	{
		return $this->result['status']['success'];
	}
	public function setSuccess($value)
	{
		$this->result['status']['success'] = $value;
	}
	/**
	 * 
	 * @return integer the status of the call
	 */
	public function getStatusCode()
	{
		return $this->result['status']['statuscode'];
	}
	/**
	 * set the status of the call
	 * 
	 * @param integer $code
	 */
	public function setStatusCode($code)
	{
		$this->result['status']['statuscode'] = $code;
	}
	public function getMessage()
	{
		return $this->result['status']['message'];
	}
	public function setMessage($message)
	{
		$this->result['status']['message'] = $message;
 	}
	/**
	 * Set an error to return to the user. Set success to false
	 * @param string $key
	 * @param string $text
	 */
	public function addError($key, $text)
	{
		$this->result['status']['errors'][$key] = $text;
		$this->result['status']['success'] = false;
	}
	/**
	 * merges multiple errors into the error status
	 * 
	 * @param array $errors
	 */
	public function addErrors($errors)
	{
		foreach($errors as $attribute=>$error) {
			if (is_array($error)) {
				foreach($error as $e) {
					$this->addError($attribute, $e);
				}
			}	else {
				$this->addError($attribute, $error);
			}
		}	
	}	
	public function hasErrors()
	{
		return $this->result['status']['success'] == false;
	}
	
	public function getData()
	{
		return $this->result['data'];
	}
	public function setData($value)
	{
		$this->result['data'] = $value;
	}
	
	/**
	 * Returns the JSON string of the object
	 * 
	 * @param boolean $return if true the JSON string is returned
	 * @return string
	 */
	public function asJson($return = false)
	{
		if ($return) {
			return CJSON::encode($this->result);
		} else {
			echo CJSON::encode($this->result);
		}
	}
	
	public function asData()
	{
		echo CJSON::encode($this->result['data']);
	}
	
	
	/**
	 * Set the error to this in the data buffer
	 * 
	 * @param integer code			the 4xx code
	 * @param string $message
	 * @param array $params
	 */
	public function raiseError($code, $message, $params = array()) 
	{
		$this->success = false;
		$this->statusCode = $code;
		$this->message = $message;
		$this->addErrors($params);
		$this->asJson();
		Yii::app()->end();
	}
	
	protected function fixBooleans($data) 
	{
		$result = array();
		foreach ($data as $key => $value) {
			if ($value === 'true') {
				$result[$key] = 1;				
			} elseif ($result[$key] === 'false') {
				$result[$key] = 0;
			} else {
				$result[$key] = $value;
			}
		}
		return $result;
	}
	/**
	 * changes camel case to unscope
	 * and 'false' false and 'true' to true
	 * @param type $datac
	 */
	protected function fixPost($data)
	{
		$result = array();
		foreach ($data as $key => $value) {
			$result[$key] = $value;		// remember the ofiginal value
			$fieldname = Util::fromCamelCase($key);
			if ($value === 'true') {
				$result[$fieldname] = 1;				
			} elseif ($result[$key] === 'false') {
				$result[$fieldname] = 0;
			} else {
				$result[$fieldname] = $value;
			}
		}
		return $result;
	}
	
	/**
	 * convert a record definition into an array
	 * 
	 * 
	 * @param CMap or CRecord $data
	 * @param array $format
	 * @return array
	 */
	public function makeJson($data, $format)
	{
		$result = array();
		// data can be an object CActiveRecord or array of CActiveRecord
		if (is_object($data)) {
			$useIndex = false;
			$data = array($data);
		} else {	
			$useIndex = true;
		}
		foreach ($data as $index => $record) {			
			if ($useIndex) {
				$result[$index] = array();
			}
			foreach ($format as $key => $field) {
				
				if (is_numeric($key)) {			// 3 => 'name' or 3 => array(...)
					if (is_string($field)) {		// 3 => fieldnam
						$keyName = $field; 
						if (isset($record->$field)) {
							$value = $record->$field;														
						} else {
							Yii::log('Field is unknown of null: '.$field, CLogger::LEVEL_INFO, 'toxus.json.controller');
							$value = '';
						}
					} elseif (is_array($field)) {		// what would that mean??
						Yii::log('Unknown definition: [number] => array(..)', CLogger::LEVEL_ERROR, 'toxus.json.controller');
					} else {
						Yii::log('Unknown definition: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.controller');
					} 
				} elseif (is_string($key)) {  
					if (is_string($field)) { // 'is_temp' => 'isTemp'
						if (isset($record->$key)) {
							$value = $record->$key;
							$keyName = $field;
						} else {
							Yii::log('Unknown field: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.controller');
						}
					} elseif (is_array($field)) { // user => array('id', 'username')
						if (isset($record->$key)) {
							$keyName = $key;
							$value = $this->makeJson($record->$key, $field);
						} else {
							Yii::log('Unknown relation: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.controller');
						} 
					} else {
						Yii::log('Unknown key type: '.$key, CLogger::LEVEL_ERROR, 'toxus.json.controller');
					}		
				}
//				if (!$value) $value = 0;
				if ($useIndex) {
					$result[$index][$keyName] = $value;
				} else {
					$result[$keyName] = $value;
				}
			}	
		}
		return $result;
	}
	
}
