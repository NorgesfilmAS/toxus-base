<?php

class ProcessState  extends CComponent
{
	/**
	 * the name of the sync file in the runtime directory
	 * 
	 * @var string
	 */
	public $filename = 'sync.json';	
	private $_state;
	
	
	public function __construct($filename = false) {
		if ($filename) {
			$this->filename = $filename;
		}
	}
	
	/**
	 * return 1 if the process is running
	 * @return integer
	 */
	public function getIsRunning()
	{
		$this->load();
		return (isset($this->_state['isRunning']) && $this->_state['isRunning'] == 1) ? 1 :0;
	}
	public function setIsRunning($value) 
	{
		$this->load();
		$this->_state['isRunning'] = $value ? 1 :0;
		$this->_state['time'] = time();
		$this->_state['startup'] = 0;
		$this->save();
	}
	
	/**
	 * returns 1 if the process must start
	 * call ProcessState->isRunning = true to mark as started
	 * does an auto reset on marked running
	 * 
	 * @return integer
	 */
	public function getStartup()
	{
		$this->load();
		return (isset($this->_state['startup']) && $this->_state['startup'] == 1) ? 1 : 0;
	}
	
	public function setStartup($value) 
	{
		$this->load();
		$this->_state['startup'] = $value ? 1 :0;
		$this->save();
	}
	
	public function setValue($key, $value) 
	{
		$this->load();
		$this->_state[$key] = $value;
		$this->save();
	}
	public function getValue($key, $default = false)
	{
		$this->load();
		return isset($this->_state[$key]) ? $this->_state[$key] : $default;
	}
	
	public function touch()					
	{
		$this->load();
		$this->_state['touch'] = date("Y-m-d H:i:s");
		$this->save();
	}
	
	public function asJson()
	{
		$ret = array(
			'isRunning' => $this->isRunning,
			'startup' => $this->startup	
		);
		if (is_array($this->_state)) {
			foreach ($this->_state as $key => $value) {
				if (!in_array($key, array('isRunning', 'startup'))) {
					$ret[$key] = $value;
				}
			}
		}	
		return CJSON::encode($ret);
	}
	
	protected function save()
	{
		if (!Util::filePutContents($this->stateFilename, CJSON::encode($this->_state, 0777) )) {
			throw new CException(Yii::t('base', 'Can not get lock on state file'));
		}
	}

	protected function load()
	{
		$state = Util::fileGetContents($this->stateFilename);
		if ($state === false) {
			throw new CException(Yii::t('base', 'Can not get lock on state file'));
		}
		$this->_state = CJSON::decode($state);
	}

	public function getStateFilename()
	{
		return YiiBase::getPathOfAlias('application.runtime').'/'.$this->filename;
	}
	
	
}