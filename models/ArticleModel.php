<?php
/**
 * based upon 2 json files name articles.json:
 *   - one in the config directory		   - the dev written files
 *   - one in the config/user directory	 - the website written files
 * 
 * article has the structure:
 *   id => array(
 *			id,
 *      title,
 *      content,
 *      className,
 *      groups 
 * 
 * the id of the article is guid of 20. Should be unique
 */
class ArticleModel extends CModel
{
	
	private $_attributeNames = array(
		'id' => 'Id',
		'title' => 'Title',
		'key'	=> 'Key',	
		'content' => 'Content',
		'className' => 'Class name',
		'groups' => 'Groups',	
		'showGeneral' => 'Show general',
		'showModerator' => 'Show moderators',	
		'showAdmin' => 'Show admin',
	);
	
	private $_d = false;
	private $_isNewRecord = true;
	
	/**
	 * must do:
	 *  $art = ArticleModel::model()->findByPk(1);
	 * @var t
	 */
	
	protected $_articles = false;
	
	
	public function rules() {
		return array(
			array('title,key', 'required'),
			array('content, className,showGeneral, showModerators,showAdmin', 'safe')
		);		
	}
	
	/**
	 * return true if this installation it the production version
	 * the 
	 */
	private function isDevelopment()
	{
		return Yii::app()->config->help['store_system'];
	}


	public function attributeNames() 
	{
		return array_keys($this->_attributeNames);
	}	
	static function model($data = false) 
	{
		return new ArticleModel($data);
	}
	
	public function __construct($data = false) {
		if ($data) {
			$this->_d = $data;
		};
	}
	
	public function onBeforeSave()
	{
		return true;
	}
	public function onAfterSave()
	{
		return true;
	}
	public function onBeforeFind()
	{
		return true;
	}
	public function onAfterFind()
	{
		return true;
	}
	
	protected function getPath($isDev)
	{
		return YiiBase::getPathOfAlias($isDev ? 'application.config.articles': 'application.config.users.articles').'.json';
	}
	
	protected function getArticles()
	{
		if ($this->_articles === false) {
			$path = $this->getPath(true);
			if (file_exists($path)) {
				$json = CJSON::decode(file_get_contents($path));
			} else {
				$json = array();
			}
			$path2 = $this->getPath(false);
			if (file_exists($path2)) {
				$json = $json +  CJSON::decode(file_get_contents($path2));				
			}
			$this->_articles = $json;
		}
		return $this->_articles;
	}
	
	protected function populateRecord($id)
	{
		$this->_d = array();
		$d = $this->_articles[$id];
		foreach ($this->attributeNames() as $name) {
			$this->_d[$name] = isset($d[$name]) ? $d[$name] : null;
		}	
		$this->_isNewRecord = false;
	}
	
	public function __get($name)
	{
		if (isset($this->_attributeNames[$name])) {
			return $this->_d[$name];
		} else {	
			return parent::__get($name);
		}	
	}
	public function __set($name, $value)
	{
		if (isset($this->_attributeNames[$name])) {
			$this->_d[$name] = $value;
		} else {
			parent::__set($name, $value);
		}
	}
	public function __isset($name) {
		if (isset($this->_attributeNames[$name])) {
			return true;
		} else {
			return parent::__isset($name);
		}	
	}
	
	
	
	public function findByPk($id)
	{
		if (isset($this->articles[$id])) {
			$this->onBeforeFind();
			$this->populateRecord($id);
			$this->onAfterFind();
			return $this;
		} else {
			return null;
		}
	}
	
	/**
	 * 
	 * @param string | false $condition text should be in title or content
	 */
	public function findAll($condition = false)
	{
		$d = array();
		foreach ($this->articles as $key => $article) {
			//$p = stripos($condition, $article['title']);
			if ($condition == false ||
					stripos($article['title'], $condition) !== false ||
					stripos($article['content'],$condition) !== false) {			
				$d[$article['title']] = ArticleModel::model($article);
			}
		}
		ksort($d);
		return $d;
	}

	public function findByKey($key = '', $option = array())
	{
		$lowerKey = strtolower($key);
		foreach ($this->articles as $id => $article) {
			if (isset($article['key']) && strtolower($article['key']) == $lowerKey) {
				$this->populateRecord($id);
				return $this;				
			}	
		}
		return null;
	}
	
	/**
	 * save the information into one of the two json 
	 * if isDev = true:   config/articles.json otherwise in config/setup/article.json
	 * 
	 * @param boolean $isDev
	 */
	public function save()
	{
		$isDev = $this->isDevelopment();
		if ($this->getIsNewRecord()) {
			return $this->insert($isDev);
		} else {
			return $this->update($isDev);
		}
	}
	
	public function insert($isDev)
	{
		if ($this->onBeforeSave()) {
			$this->_d['id'] = mt_rand(0, 10000000000); //Util::generateRandomString(20);
			$path = $this->getPath($isDev);
			if (file_exists($path)) {
				$json = CJSON::decode(file_get_contents($path));
			} else {
				$json = array();
			}	
			$json[$this->_d['id']] = $this->_d;
			file_put_contents($path, CJSON::encode($json));
			$this->onAfterSave();
			$this->_articles = false;	// clear our article buffer
			$this->_isNewRecord = false;
			return true;
		} else {
			return false;
		}
	}
	public function update($isDev)
	{
		if ($this->onBeforeSave()) {
			$path = $this->getPath($isDev);
			if (file_exists($path)) {
				$json = CJSON::decode(file_get_contents($path));
			} else {
				throw new CDbException('The definition of '.$path.' could not be found');
			}				
			if (!isset($json[$this->_d['id']])) {
				throw new CDbException('The record id '.$this->_d['id'].' could not be found');
			}
			$json[$this->_d['id']] = $this->_d;
			file_put_contents($path, CJSON::encode($json));
			$this->onAfterSave();
			$this->_articles = false;	// clear our article buffer
			return true;
		} else {
			return false;
		}
	}
	
	
	public function getIsNewRecord()
	{
		return $this->_isNewRecord;
	}
}