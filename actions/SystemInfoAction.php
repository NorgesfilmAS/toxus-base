<?php

class SystemInfoAction extends CAction
{
	
	/**
	 * Load extra parameters 
	 * 
	 * set to array($this, functionName). functionName($properties) return $properties
	 * 
	 * @var array
	 */
	public $onExtraInfo = null;
	/**
	 * set the view to display. Default is systemInfo, or if $_GET['dialog']==1 systemInfoDialog
	 * @var string
	 */
	public $view = 'systemInfo';
	
	public function run()
	{
		$prop = array(); //array('database', 'params', 'setup');
		$items = array();
		$items['db-connection'] = Yii::app()->db->connectionString;
		$items['db-username'] = Yii::app()->db->username;
		$items['db-charset'] = Yii::app()->db->charset;
		$prop['database'] = array(
			'caption' => 'database',
			'items' => $items,					
		);
		$param = array();
		$param['firePHP'] = Yii::app()->params['firePHP'] == 1 ? 'On' : 'Off';
		$prop['params'] = array(
			'caption' => 'parameters',
			'items' => $param,					
		);
		
		$server = array();
		$server['baseUrl'] = Yii::app()->request->baseUrl;
		$server['hostInfo'] = Yii::app()->request->hostInfo;
		$server['isSecureConnection'] = Yii::app()->request->isSecureConnection;
		$server['pathInfo'] = Yii::app()->request->pathInfo;
		$server['requestUri'] = Yii::app()->request->requestUri;
		$server['scriptFile'] = Yii::app()->request->scriptFile;
		$server['scriptUrl'] = Yii::app()->request->scriptUrl;
		$server['serverName'] = Yii::app()->request->serverName;
		$server['url'] = Yii::app()->request->url;
		$server['userHostAddress'] = Yii::app()->request->userHostAddress;
		$server['userHost'] = Yii::app()->request->userHost;
		$prop['request'] = array(
			'caption' => 'request',
			'items' => $server,					
		);
						
		/** the system setup */
		$setup = array();
		$setup['max post size'] = array('value' => Util::maxPostSize(false), 'explain' => 'Set in .htaccess through: php_value post_max_size 16M');
		$setup['max file upload size'] = array('value' => Util::maxFileUploadSize(false), 'explain' => 'Set in .htaccess through php_value upload_max_filesize [n]M');
		$prop['setup'] = array(
			'caption' => 'Server setup',
			'items' => $setup,					
		);
		
		
		//$prop['user'] = Yii::app()->user->id;
		if (isset($_GET['dialog']) && $_GET['dialog'] == '1') {
			$this->view = 'systemInfoDialog';
		}
		if ($this->onExtraInfo != null) {
			$prop = call_user_func_array($this->onExtraInfo, array($prop));
		}
		$this->controller->render($this->view, array('properties' => $prop));
	}
}
