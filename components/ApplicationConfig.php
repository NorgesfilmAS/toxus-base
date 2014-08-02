<?php
/**
 * This file contains system setup definition.
 *
 * @author Jaap van der Kreeft <jaap@toxus.nl>
 * @link http://www.toxus.nl/
 * @copyright 2014 Toxus
 * @license Mit
 */
/**
 * version 1.0 18 feb 2014 
 * 
 * the configuration of a system
 * Default the 
 *   - application/config/setup.local.php
 * is loaded.
 * User configurate is store in $configPath/[userId].json as JSON definition
 * 
 * Developer can use:
 *    Yii::app()->config->setup['hasCustomers']
 * 
 * To use user specfic definition the call to
 *   Yii::app()->config->mergeConfig($userKey) 
 * must be called
 * Multiple calls to mergeConfig can be done to create customers / groups / etc.
 * The later merged overload the previous values!
 */

class SectionProperties extends CMap
{
	/**
	 * the raw information from the configuration definition
	 * @var array
	 */
	private $_raw;
	/**
	 * 
	 * @param array $data the configuration section
	 * @param type $readOnly
	 */
	public function __construct($data = null, $readOnly = false) {
		$this->_raw = $data;
		$values = array();
		if (isset($data['items'])) {
			foreach ($data['items'] as $name => $property) {
				$values[$name] = $property['value'];
			}
		}
		parent::__construct($values, $readOnly);
	}
	
	/**
	 * checks if there are editable properties in the section
	 * @return boolean true if all properties are locked
	 */
	public function isLocked()
	{
		foreach ($this->_raw['items'] as $key => $properties) {
			if (isset($properties['lock']) && $properties['lock'] > 0) {
				// this is lock property
			} else {
				return false;
			}
		}
		return true;
	}
	/**
	 * @returns array the raw data
	 */
	public function definition()
	{
		return $this->_raw;
	}
	public function isEditable()
	{
		return (!isset($this->_raw['is_editable'])) || $this->_raw['is_editable'];
	}
}

class ApplicationConfig extends CComponent
{
	/**
	 * the configuration definition to load at startup
	 * @var string, array
	 */
	public $configuration = 'setup';
	
	/**
	 * internal for the logging
	 */
	const logKey = 'toxus.ApplicationConfig';
	
	/**
	 * the path to the directory where the customer / user configuration
	 * is stored
	 * 
	 * @var string
	 */
	const CONFIG_PATH = 'application.config.users';
	
	/**
	 * the internal array holding the configuration
	 * 
	 * @var array
	 */
	private $_config;
	
	/**
	 * if false there can still be loadOnDemand items in the sections
	 * @var boolean
	 */
	
	/**
	 * opens the system configuration with out merging the setup specific infor
	 *
	 * @returns array the arry definition of the setup  
	 */
	private function openConfig() 
	{
		$path = YiiBase::getPathOfAlias(self::CONFIG_PATH);	
		if (!is_dir($path)) {
			Yii::log('Generating configuration path: '.$path,  CLogger::LEVEL_INFO, self::logKey.'.init');
			@mkdir($path);
			@chmod($path, 0777);
		}
		$systemSetupPath = YiiBase::getPathOfAlias('application.config.setup-local').'.php';
		Yii::log('Using setup configuration '.$systemSetupPath, CLogger::LEVEL_INFO, self::logKey.'.init');
		// loading all information from the programmers view
		if (!file_exists($systemSetupPath)) {
			throw new CException(Yii::t('base', 'The system setup file ({filename}) does not exist.',
							array('{filename}' => $systemSetupPath)));
		}
		return require($systemSetupPath);
	}
	
	public function init()
	{
		// load the system wide config
		
		$this->_config = $this->openConfig();
		// check for any 'bound' parameters
		foreach ($this->_config as $section => $properties) {
			foreach ($properties['items'] as $key => $property) {
				if (isset($property['bound']) && $property['bound']) {
					Yii::app()->setComponent($section, array($key => $property['value']));
				}
			}
		}
		// load setup configuration
		if ($this->configuration) {
			if (!is_array($this->configuration)) {
				$this->configuration = array($this->configuration);
			}	
			foreach ($this->configuration as $conf) {
				$this->mergeConfig($conf);
			}
		}
	}
	
	/**
	 * return the requested section as an array
	 * usage:
	 *    $cfg->security['password']
	 *    get will return the security part of it
	 * 
	 * @param string $name
	 * @return CMap / PropertyArray
	 * @throws CException if section not found
	 */
	public function __get($name)
	{
		if (isset($this->_config[$name])) {
			if (!isset($this->_config[$name]['_data'])) {
				$this->_config[$name]['_data'] = new SectionProperties($this->_config[$name]);
			}
		  return $this->_config[$name]['_data'];
		} else {
			$funcName = 'get'.$name;
			if (method_exists($this, $funcName))
				return $this->$funcName();
			throw new CException(Yii::t('base', 'Configuration section "{section}" does not exists', array('{section}' => $name)));
		}
	}
	/**
	 * set the definition of a section
	 * The section must be preloaded with the key='xxx' statement
	 * 
	 * @param string $name
	 * @param array $values the array definition of the section
	 */
	public function __set($name, $values)
	{
		if (isset($this->_config[$name])) {
			if (isset($this->_config[$name]['loadOnDemand']) && $this->_config[$name]['loadOnDemand']) {
				if (isset($this->_config[$name]['_items'])) {
					$value['items'] = $this->mergeWithLocked($values['items'], $this->_config[$name]['_items'], $this->config[$name]['_levelId']);
				} 
				$this->_config[$name] = $value;
			} else {
				throw new CException(Yii::t('base', 'Section {section} is not load on demand. Could be section is already loaded', array('{section}' => $name)));
			}
		} else {
			throw new CException(Yii::t('base','The section {section} is not defined. Use the key in the App section.',array('{section}'=>$name)));
		}
	}
	
	public function __isset($name) {
		return isset($this->_config[$name]);
	}
	/**
	 * returns the array of sections loaded.
	 * All loadOnDemand are called before loading
	 * modifying items in this return array does not reflect in the name=>value pairs
	 * use the $cfg->$section[$name] = value syntax
	 * 
	 */
	public function sections()
	{
		return $this->_config;
	}
	/**
	 * 
	 * @param type $config
	 * @param type $levelId
	 */

	private function mergeWithLocked(&$config, $levelId)
	{
		foreach ($config as $section => $definition) {
			if (isset($this->_config[$section])) {
				foreach ($definition as $param => $value) {
					if (isset($this->_config[$section]['items'][$param])) {
						$this->_config[$section]['items'][$param]['value'] = $value;
						if (isset($this->_config[$section]['items'][$param]['bound'])) {
							Yii::app()->setComponent($section, array($param => $value));
						}
					}
				}
			} else {
			Yii::log('Section not defined '.$section, CLogger::LEVEL_WARNING, self::logKey.'.mergeWithLocked');
			}
		}	
	}
	
	/**
	 * Merge an extra definition into the setup configuration. The configuration
	 * should be stored in the $configPath
	 * 
	 * @param string $configId the unique id to merge
	 * @data array | null the configuration to load
	 * @return boolean false if config did not exist
	 */
	public function mergeConfig($configId, $data = null)
	{
		if ($data == null) {
			$path = YiiBase::getPathOfAlias('application.config.users'.'.'.$configId).'.json';
			if (!file_exists($path)) {
					Yii::log('Configuration does not exist: '.$path, CLogger::LEVEL_WARNING, self::logKey.'.loadUserConfig');
					return false;
			}
			$customerConf = CJSON::decode(file_get_contents($path));
		} else {
			$customerConf = $data;
		}	
		$this->mergeWithLocked($customerConf, $configId);
		return true;
	}
	/**
	 * Save the changed information to the user configuration file
	 * 
	 * @param string $configId the id to save to
	 * @return boolean true if all went well
	 */
	public function save($configId)
	{
		/*
		$originalConfig = new ApplicationConfig();
		$originalConfig->configuration = $this->configuration;
		$originalConfig->init();
		*/
		$originalConfig = $this->openConfig();
		
		$result = array();
		foreach ($originalConfig as $sectionName => $section) {
			$sec = $this->$sectionName;
			foreach ($section['items'] as $varName => $properties) {			
				$org = $properties['value'];
				$chg = $sec[$varName];
				if ($org != $chg) {
					if (isset($result[$sectionName])) {
						$result[$sectionName][$varName] = $chg;
					} else {
						$result[$sectionName] = array($varName => $chg);
					}
				}
			}
		}
		$path = YiiBase::getPathOfAlias('application.config.users'.'.'.$configId).'.json';
		if (count($result) > 0) {
			file_put_contents($path, CJSON::encode($result));
		} else { // remove the definintion
			if (file_exists($path)) {
				@unlink($path);
			}
		}
		return true;		
	}

}