<?php
/**
 * holds all information about an result jsom object
 * 
 * the object is returned as two object system:
 *   status : holding all status information
 *   result : holding the data
 * 
 */
class JsonResult
{
	/*
	 * the result is an success
	 */
	public $isOk = true;
	/*
	 * the error message to the users
	 */
	public $message = false;
	/**
	 * the name => error values
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * the result array
	 * @var array
	 */
	public $result = false;
	
	public function asJson($return = true)
	{
		$a = array(
			'status' => array(	
				'success' => $this->isOk,
				'message' => $this->message,				
				'errors' => $this->errors,
			),		
		);

		if ($result) {
			$a['result'] = $this->result;
		}
		if ($return) {
			return CJSON::encode($a);
		} else {
			echo CJSON::encode($a);
		}	
	}
}