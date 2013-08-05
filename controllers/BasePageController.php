<?php
/**
 * controller that shows static pages, which are in twig style
 * pages are fysically store on disk in appplication.views.pages 
 * 
 * pages can be edited online (if application.views.pages is writable!)
 */

class BasePageController extends Controller
{
	const ROOT_ALIAS = 'application.views.page';
	
	public function actionIndex($file='index')
	{
		if ($file == 'create'){
			$this->actionCreate();
		} elseif ($file == 'update') {
			$this->actionUpdate($_GET['page']);
		} else {
			$filename = $this->viewPath($file);
			if (!$filename) {
				throw new CHttpException(404, 'Page not found.',2);
			}
			$this->model = new FileContent;
			$this->model->page = $file;
			$params = array(
				'mode' => 'view',
			);
			$this->render($file, $params);				
		}
	}

	/**
	 * updates the information on the page.
	 * Page can be renamed. All pages are stored in the views.page directory.
	 * Default pages can be overloaded from the vendor.views.page directory
	 * 
	 * @param string $page the name on disk of the page
	 * @throws CHttpException
	 */
	public function actionUpdate($page='index')
	{
		/** 
		 * check user has sufficient rights
		 */
		
		$this->model = new FileContent;
		if ($_POST['Page'] && isset($_POST['Page']['content'])) {
			$this->model->content = $_POST['Page']['content'];
			$this->model->page = trim($_POST['Page']['page']);
			$this->model->originalName = trim($_POST['Page']['originalNamee']);
			if ($this->model->page == '') {
				$this->model->addError('There is no filename');					
			} else {
				$filename = Yii::getPathOfAlias(self::ROOT_ALIAS).'/'.$this->model->page.'.twig';
				try {
					if (!is_writable(Yii::getPathOfAlias(self::ROOT_ALIAS))) {
						$this->model->addError('Can not write to the selected directory. (filename: '.$filename.')');					
					} else {
						file_put_contents($filename, $this->model->content);
						if ($this->model->page != $this->model->originalName) {
							$p = Yii::getPathOfAlias(self::ROOT_ALIAS).'/'.$this->model->originalName.'.twig';
							if (file_exists($p))	// can be a standard file
								unlink($p);
						}
						$this->redirect($this->createUrl('page/a').'/'.$page);
					}	
				} catch (Exception $e) {
					$this->model->addError($e->getMessage());
				}
			}	
		} else {
			$filename = Yii::getPathOfAlias('application').'/'.$this->viewPath($page);// Yii::getPathOfAlias('application').'/'.$filename;		
			if (!$filename) {
				throw new CHttpException(404, 'Page not found.',2);			
			}	
			$this->model->content = file_get_contents($filename);							
			$this->model->page = $page;
			$this->model->originalName = $page;			
		} 
		$form = $this->loadForm('pageFields');					
		$this->model->id = $page;		
		$this->render(
			'edit', array(			
			'form' => $form,
			'mode' => 'edit',		
		));						
	}
	public function actionCreate()
	{
		$this->model = new FileContent;
		if ($_POST['Page'] && isset($_POST['Page']['content'])) {
			$this->model->content = $_POST['Page']['content'];
			$this->model->page = trim($_POST['Page']['page']);
			if ($this->model->page == '') {
				$this->model->addError('There is no filename');					
			} else {
				$filename = Yii::getPathOfAlias(self::ROOT_ALIAS).'/'.$this->model->page.'.twig';
				try {
					if (!is_writable(Yii::getPathOfAlias(self::ROOT_ALIAS))) {
						$this->model->addError('Can not write to the selected directory. (filename: '.$filename.')');					
					} elseif (file_exists($filename)) {
						$this->model->addError('The file aready exists. (filename: '.$filename.')');					
					} else {
						file_put_contents($filename, $this->model->content);
						$this->redirect($this->createUrl('page/a').'/'.$this->model->page);
					}	
				} catch (Exception $e) {
					$this->model->addError($e->getMessage());
				}
			}	
		} else {
			$this->model->content = "{% extends this.viewPath('mainPage') %}\n\n{# file information #}\n{% block content %}\n\n{% endblock %}\n";
		} 
		
		$form = $this->loadForm('pageFields');					
		$this->model->id = '';		
		$this->model->page = '';
		$this->render(
			'edit', array(			
			'form' => $form,
			'mode' => 'edit',					
		));								
	}
	/**
	 * list all the pages defined
	 */
	public function actionList()
	{
		$files = array();
		$path = Yii::getPathOfAlias(self::ROOT_ALIAS);
		foreach (new DirectoryIterator($path) as $fileInfo) {
			if (!$fileInfo->isDot()) {
				if ($fileInfo->isFile()) {
					$s = substr($fileInfo->getBasename(), 0, strlen($fileInfo->getBasename()) - strlen($fileInfo->getExtension()) -1);
					if ($s != '')
						$files[] = $s;
				}
			}	
		}
		$this->render('listFiles', array('files' => $files));
	}
	
}

class FileContent
{
	private $_data = array();
	private $_errors = array();
	
	
	public function __get($key)
	{
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		return null;
	}
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	public function __isset($name) {
		return isset($this->_data[$name]);
	}
	
	public function attribute($key)
	{
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		return null;		
	}
	
	public function attributeLabel($key)
	{
		return Yii::t('app', $key);
	}
	
	
	public function hasErrors()
	{
		return count($this->_errors) > 0;
	}
	public function addError($s)
	{
		$this->_errors[] = $s;
	}
	public function getErrors()
	{
		return array($this->_errors);
	}
}