<?php
/**
 * configure the system on runtime
 * 
 */

class RuntimeConfig extends CComponent
{
	private $_data;
	private $_eventObjects = array();
	
	
	
	public function __get($name) 
	{	
		if (isset($this->_eventObjects[$name])) {
			return $this->_eventObjects[$name];
		}
		return parent::__get($name);
	}
	/**
	 * load the customer specific information
	 */
	public function init()
	{
		$filename = Yii::getPathOfAlias('application.config').'/config.php';
		$this->_data = require($filename);
	/**
	 * user configurable
	 
		$customer = Yii::app()->user->customer;
		if ($customer->is_active) {
			$config = $customer->filePath.'/config.php';
			if (file_exists($config)) {
				$this->_data = array_merge(
						$this->_data,		
						require($config)
				)	;
			}	
		} 
	 * 
	 */
	}
	
	/**
	 * bind the event(s) to the class
	 * 
	 * @param object $object the object to connect to
	 * @param eventName the name of the event to connect to
	 * @param array $classes an array of {class} => {function} to be connected
	 * 
	 */
	private function bindEvent($object, $eventName, $classes)
	{
		foreach ($classes as $class => $func) {
			if (!isset($this->_eventObjects[$class]))
				$this->_eventObjects[$class] = new $class;
			$object->$eventName = array($this->_eventObjects[$class], $func);	
		}
	}
	/**
	 * connect the events to the object. Any object can be used (expecting CComponent)
	 * 
	 * @param object $object the object ot connect to (CComponent)
	 * @param stringtype $eventName
	 */
	public function connectEvents($object, $eventName)
	{				
		if (isset($this->_data['events'])) {
			$classname = get_class($object);
			if (isset($this->_data['events'][$classname])) {
				$events = $this->_data['events'][$classname];
				if (isset($events[$eventName])) {
					$this->bindEvent($object, $eventName, $events[$eventName]);					
				}
			} else { // maybe it's a subclass?? using is_subclass_of
				foreach ($this->_data['events'] as $classname => $events) {
					if (is_subclass_of($object, $classname)) {
						if (isset($events[$eventName])) {
							$this->bindEvent($object, $eventName, $events[$eventName]);
						}
					}
				}
			}
		}
	}
	
}