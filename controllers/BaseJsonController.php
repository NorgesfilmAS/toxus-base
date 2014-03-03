<?php

class BaseJasonController extends CController
{
	/**
	 * to definitions: 
	 *	- status: information about the call
	 *  - data:		the data returned to caller
	 * 
	 * @var array the result returned to the caller
	 */
	public $result = array(
			'status' => array(
					'success' => true,					// if all went well
					'message' => '',						// message to the user
					'statusCode' => 200,				// http status codes
					'errors' => array(),				// a key => error text array
			),
			'data' => array(),
	);
	
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
		return $this->result['status']['statusCode'];
	}
	/**
	 * set the status of the call
	 * 
	 * @param integer $code
	 */
	public function setStatusCode($code)
	{
		$this->result['status']['statusCode'] = $code;
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
		$this->result['status']['error'][$key] = $text;
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
}
