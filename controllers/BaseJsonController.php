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
	// WHY??	Yii::app()->json;				
		parent::init();
	}
/*	
 * THIS MAKES THE INTERFACE CRASH on Mediakunst.net
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
	*/
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
	public function setError($message) 
	{
		$this->result['status']['message'] = $message;
		$this->result['status']['success'] = false;
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
			$this->jsonHeader();
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
	 * @param type $data
   * @param type array, the default expected data and the value
	 */
	protected function fixPost($data, $defaults = array())
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
    foreach ($defaults as $default => $value) {
      if (!isset($result[$default])) {
        $result[$default] = $value;
      }
    }
		return $result;
	}
	
	/**
	 * write the json header 
	 */
	protected function jsonHeader()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');		
	}
	/**
	 * convert a record definition into an array
	 * 
	 * 
	 * @param CMap or CRecord $data
	 * @param array $format
	 * @return array
	 */
	/** should be done a generator
	public function makeJson($data, $format)
	{
		$json = new JsonGenerate();
		return $json->run($data, $format);
	}
	 * 
	 */
}
