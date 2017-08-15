<?php
/**
 * generate controller for handling the json requests
 * 
 * $this->licenseKey['guid'] = customer.license_guid
 * $this->licenseKey['userId'] = user.id
 * 
 * upd: 13/05/2016 JvK so
 */

Yii::import('toxus.controllers.BaseJsonController');
// Yii::import('toxus.extensions.phpEncryption.ToxusEncryption');
class JsonController2 extends BaseJsonController
{
	/**
	 * this should be set with EVERY http call to the REST interface and 
	 * defines the access potential of the current user
	 */
	const HTTP_HEADER_KEY ='X-Session'; //'X-Authorization';
  const HTTP_HEADER_CUSTOMER = 'X-Customer';
  const ARRAY_PREFIX = 'array:';
	
	protected $allowOtherOrigin = true;
	/**
	 * the string that is being returned
	 * @var string
	 */
	protected $result = false;
	
	/**
	 * defines which actions are allowed if not logged in
	 * @var array
	 */
	protected $guestActions = array();
	
	/**
	 * the access key that defines what the current user can see and which
	 * documents can be retrieved.
	 * 
	 * It set by the initialization by decoding the http-key or
	 * if setup allowGetKey == 1 by the $_GET['key']
	 *   
	 * @var string
	 */	
	
	
	public function init() {    
		if ($this->allowOtherOrigin) {
      Yii::log('Adding other origin', CLogger::LEVEL_INFO, 'toxus.json2.init');
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Headers: X-Session');
      
      // Access-Control headers are received during OPTIONS requests
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
      }      
		}	 else {
      Yii::log('Not adding other origin', CLogger::LEVEL_INFO, 'toxus.json2.init');
    }
    
    $allHeaders = getallheaders();	// X-Session is not in the standard headers
    Yii::log('Header key:'.(isset($_SERVER[self::HTTP_HEADER_KEY]) ? $_SERVER[self::HTTP_HEADER_KEY] : '(none)'), CLogger::LEVEL_INFO, 'toxus.json.loadAccessKey');	
    if (isset($allHeaders[self::HTTP_HEADER_KEY])) {
      $key = $allHeaders[self::HTTP_HEADER_KEY];
    } else {
      $key = '';
    }
    $customer = isset($allHeaders[self::HTTP_HEADER_CUSTOMER]) ? $allHeaders[self::HTTP_HEADER_CUSTOMER] : '';
    
    Yii::app()->user->sessionKey($key, $customer);
    
		parent::init();
	}

  /**
   * 
   * @param string $customer  - the license id for the customer
   * @param string $userId    - the user that logged in
   * @return string           - the session key for the user
   */
  protected function login($customerGuid, $userId) {
    $key = array(
      'guid' => $customerGuid,
      'userId' => $userId
    );
    Yii::app()->user->setLogin($key);
    return Yii::app()->user->session->guid;
  }
  
	/**
	 * filters the none guestActions
	 * @param type $filterChain
	 */
	public function filterAccessControl($filterChain)
	{
    if (Yii::app()->user->isGuest) { // user logged in
      foreach ($this->guestActions as $action) {
        if ($filterChain->action->id == $action) {
          $filterChain->run();
          return;
        }
      }    
    } else {
      $filterChain->run();
      return;
    }
		$this->result = array(
      'status' => 403,
      'message' => 'Access denied'
    );
    $this->asJson();
		Yii::app()->end(403);
	}
	
	
	/**
	 * 
	 * @param string $paramName  the name of the parameter
	 * @param string $default    the default value if not found
	 * @param type $useGet       if true uses the get
	 */
	protected function getValue($paramName, $default = false, $useGet = false) 
	{
		if ($useGet) {
			return (isset($_GET[$paramName])) ? $_GET[$paramName] : $default;			
		} else {
			if (isset($_POST[$paramName])) {
				return $_POST[$paramName];
			} else if (Yii::app()->config->system['allowGet'] && isset($_GET[$paramName])) {
				return $_GET[$paramName];
			}
			return $default;			
		}
	}
	
	protected function returnError($code, $message=false)
	{
    throw new CHttpException($code, $message);		
	}
	
	/**
	 * Add the sessionId to the status definition	 
	 */
	protected function jsonHeader()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');		
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
	
}