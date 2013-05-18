<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 * 
 * if logPage = false no logging is done for the request, otherwise info is written
 * 
 */
class Controller extends CController
{
	public $vendorViewRoot = 'vendors.toxus.views';
	public $layout='//layouts/column1';
	public $breadcrumbs=array();
	public $brand = 'Percussive Guitar';
	public $model = null;
	protected $_logPageView = true;
	private $_menu = null;
	
	public $_pageStyles = null;	
	public $_defaultStyle = '';
	public $_pageStyle = 'default'; // should come from $model->page_style	
	public $logPageSpeed = true;    // if true the page generation speed is stored 
	
	private $_formElements;
	protected $_assetBaseUrl;
	
	

	public function getForm()
	{
		if ($this->_formElements == null) {
			$this->_formElements = new FormElements();
		}
		return $this->_formElements;
	}
	
	/**
	 * load all menu's of the system and connects external menu's
	 * @return array
	 */
	
	protected function getMenu()
	{
		if (empty($this->_menu)) {
			$this->_menu =  array(
				'system' => array(), 
				'user'=> array(),
				'main'=> array(),
				'item'=> array(),
				'toolbar'=> array(),
				'explain'=> array(),
				'footer'=> array(),
				'popup'=> array(),
				'help'=> array(),
			);
			
			/**
			 * load the system wide menu's
			 */
			foreach ($this->_menu as $name => $menu) {
				$eventName = 'on'.ucfirst($name).'Menu';
				Yii::app()->config->connectEvents($this, $eventName);

				$menuFilename = Yii::getPathOfAlias('application.views.'.$this->id.'.'.$name.'Menu').'.php';
				if (!file_exists($menuFilename)) {
					$menuFilename = Yii::getPathOfAlias('application.views.layouts.'.$name.'Menu').'.php';
					if (!file_exists($menuFilename)) {
						$menuFilename = null;
					}
				}
				if (!empty($menuFilename))
					$menu = require($menuFilename);

				$event = new CMenuEvent($this);
				$event->menu = $menu;
				$this->$eventName($event);
				$this->_menu[$name] = $event->menu;
			}			
		}
		return $this->_menu;
	}

	public function onSystemMenu($event) 
	{
		$this->raiseEvent('onSystemMenu', $event);
	}
	public function onUserMenu($event) 
	{
		// check if user is login / guest / admin / etc
		$event->menu = $this->userMenu($event->menu);
		$this->raiseEvent('onUserMenu', $event);
	}
	public function onMainMenu($event) 
	{
		$this->raiseEvent('onMainMenu', $event);
	}
	public function onToolbarMenu($event) 
	{
		$this->raiseEvent('onToolbarMenu', $event);
	}
	public function onItemMenu($event) 
	{
		$this->raiseEvent('onItemMenu', $event);
	}
	public function onExplainMenu($event) 
	{
		$this->raiseEvent('onExplainMenu', $event);
	}
	public function onFooterMenu($event) 
	{
		$this->raiseEvent('onFooterMenu', $event);
	}
	public function onPopupMenu($event) 
	{
		$this->raiseEvent('onPopupMenu', $event);
	}
	public function onHelpMenu($event) 
	{
		$this->raiseEvent('onHelpMenu', $event);
	}

/**
 * generate the HTML code for menuName, depending on what menu is available.
 * - first looks in the class.id if the file [menuItem]Menu.twig exists
 * - then looks in layouts for the file [menuItem]Menu.twig exists
 * - else uses layout/menu.twig
 * 
 * 
 * @param variant $menuDef the name of the system menu or an array['name'], array['menu'] with the definition
 * @return string echo the awnser
 */
	public function menuHtml($menuDef)
	{	
		if (is_array($menuDef)) {
			$menuName = isset($menuDef['name']) ? $menuDef['name'] : 'default';
			$menu = array($menuName => isset($menuDef['menu']) ? $menuDef['menu'] : array());
		} else {
			$menuName = $menuDef;
			$menu = $this->getMenu();
		}	
		if (!isset($menu[$menuName])) return '';
		$params = array(
			'menu' => $menu[$menuName],	
			'name' => $menuName,	
		);
		
		$templateFilename = Yii::getPathOfAlias('application.views.'.$this->id.'._'.$menuName.'Menu').'.twig';		
		if (file_exists($templateFilename)) {
			$s = $this->renderPartial('application.views.'.$this->id.'._'.$menuName.'Menu', $params, true);							
		} else {
			$templateFilename = Yii::getPathOfAlias('application.views.layouts._'.$menuName.'Menu').'.twig';
			if (file_exists($templateFilename)) {
				$s = $this->renderPartial('application.views.layouts._'.$menuName.'Menu', $params, true);							
			} else {
				$templateFilename = Yii::getPathOfAlias('application.views.layouts._menu').'.twig';
				if (file_exists($templateFilename)) {
					$s = $this->renderPartial('application.views.layouts._menu', $params, true);
				} else {
					$templateFilename = Yii::getPathOfAlias('application.'.$this->vendorViewRoot.'.layouts._'.$menuName.'Menu').'.twig';
					if (file_exists($templateFilename)) {
						$s = $this->renderPartial('application.'.$this->vendorViewRoot.'.layouts._'.$menuName.'Menu', $params, true);
					} else {	
						$templateFilename = Yii::getPathOfAlias('application.'.$this->vendorViewRoot.'.layouts._menu').'.twig';
						if (file_exists($templateFilename)) {
							$s = $this->renderPartial('application.'.$this->vendorViewRoot.'.layouts._menu', $params, true);
						} else {
							throw new CException('view file not found: '.$menuDef);
						}	
					}
				}	
			}	
		}	
		echo $s;
	}



	/**
	 * creates the usermenu
	 * 
	 * @param array $menu
	 * @return array
	 */
	
	public function userMenu($menu)
	{
		return $menu;
		if (Yii::app()->user->isGuest) {
			$menu = array(
				'sign-up' => array(
					'label' => Yii::t('app', 'menu-sign-up'),
					'url' => $this->createUrl('login/new'),
					'icon' => 'icon-user',	
				),
				'sign-in' => array (
					'label' => Yii::t('app', 'menu-sign-in'),
					'url' => $this->createUrl('login/index'),						
				),	
			);
		} else {
			$menu = array(
				'sign-out' => array (
					'label' => Yii::t('app', 'menu-sign-out'),
					'url' => $this->createUrl('login/logout'),						
				),	
			);
		}
	  return array('menu' => $menu);
	}
	
	public function sniplet($template, $params=array())
	{
		$filename = $this->getViewFile($template);
		if (! $filename ) {
			$filename = YiiBase::getPathOfAlias('application.views.layouts.'.$template).'.twig';	
			if (!file_exists($filename)) return '';
		}
		return $this->renderFile($filename, $params);
	}
	
	/**
	 * interfaces between the twig and yii 
	 *
	 */
	
	public function x()
	{
		return 'im x';
	}
	public function registerCssFile($filename, $media = 'screen')
	{
		if (substr($filename, 0, 7) == 'http://') {
			Yii::app()->getClientScript()->registerCssFile($filename, $media);
		} else {
			Yii::app()->getClientScript()->registerCssFile(yii::app()->request->baseUrl.'/css/'.$filename, $media);
		}	
	}
	public function registerScriptFile($filename, $atEnd = true)
	{
		Yii::app()->getClientScript()->registerScriptFile(yii::app()->request->baseUrl.'/js/'.$filename, $atEnd ? CClientScript::POS_END : CClientScript::POS_HEAD);
	}
	
	public function registerScript($name, $script, $atEnd = CClientScript::POS_END)
	{
		Yii::app()->getClientScript()->registerScript($name, $script, $atEnd);
	}
	
	public function registerCore($part)
	{
		Yii::app()->clientScript->registerCoreScript($part);
	}

	public function getAssetsBase()
	{				
		if ($this->_assetBaseUrl === null) {
			$this->_assetBaseUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('toxus.assets'));
		}
		return $this->_assetBaseUrl;
	}	
	
	public function registerCoreScriptFile($filename, $atEnd = true)
	{
		Yii::app()->getClientScript()->registerScriptFile($this->assetsBase.'/js/'.$filename, $atEnd ? CClientScript::POS_END : CClientScript::POS_HEAD);		
	}
	public function registerCoreCssFile($filename, $media = 'screen')
	{
		if (substr($filename, 0, 7) == 'http://') {
			Yii::app()->getClientScript()->registerCssFile($filename, $media);
		} else {
			Yii::app()->getClientScript()->registerCssFile($this->assetsBase.'/css/'.$filename, $media);		
		}	
	}
	
	public function addHeader($header='X-UA-Compatible: IE=edge,chrome=1')
	{				
		header($header);		
	}
	
	/**
	 * translate a message
	 * 
	 * @param string $msg
	 * @param array $params
	 */
	public function t($msg, $params=array(), $return = false)
	{
		$m = ucfirst(Yii::t('app', $msg, $params));		
		if (! is_array($params) || $return )
		  return $m;	
		else {
		  echo $m; 
		}
	}
	public function te($msg, $params=array())
	{
		return ucfirst(Yii::t('app', $msg, $params));
	}
	
	public function flash()
	{	
		$flashMessages = Yii::app()->user->getFlashes();
		if ($flashMessages) {
			$this->render('//layouts/_flash', array('flashes' => $flashMessages));
		}				
	}
	
	protected function formAdjust(&$form, $isNew = true)
	{
		if ($isNew) {
			$form['title'] = $this->t('new',1).' '.$form['title'];
			$form['buttons']['submit']['label'] = $this->t('create', 1);
		}
		return $form;
	}
/**	
	public function htmlEditor($model, $attribute, $template='full', $options=array())
	{
		$this->widget(
				'application.extensions.etinymce.ETinyMce',
				array(
						'model'=> $model,
						'attribute' => $attribute,
						'editorTemplate' =>'full',
						'useSwitch' => false,
						'options' => array(
								'theme_advanced_buttons1' =>
								'undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent, indent,|,|,sub,sup,|,bullist,numlist,|,code,|,image',
								'theme_advanced_buttons2' => 'formatselect,|,cut,copy,paste,pastetext,pasteword,|,search,replace, tablecontrols,|,removeformat,visualaid,',
								'theme_advanced_buttons3' => '',
								'theme_advanced_buttons4' => '',
//								'theme_advanced_buttons4' => '',
								'theme_advanced_toolbar_location' => 'top',
								'theme_advanced_toolbar_align' => 'left',
								'theme_advanced_statusbar_location' => 'none',
								'theme_advanced_font_sizes' => "10=10pt,11=11pt,12=12pt,13=13pt,14=14pt,15=15pt,16=16pt,17=17pt,18=18pt,19=19pt,20=20pt",
//								'force_br_newlines' => true,
//								'force_p_newlines' => false,
//								'forced_root_block' => '',
//								'plugins' => 'autoresize',
						)
          )
			);		
	}
 * 
 */
	public function htmlEditor($model, $attribute, $template='full', $options=array())
	{
		$this->widget('ext.tinymce.TinyMce', array(
				'model' => $model,
				'attribute' => $attribute,
				// Optional config
				'compressorRoute' => false, //'tinyMce/compressor',
				//'spellcheckerUrl' => array('tinyMce/spellchecker'),
				// or use yandex spell: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
				'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
				'fileManager' => array(
						'class' => 'ext.elFinder.TinyMceElFinder',
						'connectorRoute'=>'elFinder/connector',
				),
				'htmlOptions' => array(
						'rows' => 6,
						'cols' => 100,
						'class' => 'span10',
				),
				'settings' => array(
							'theme_advanced_buttons1' =>
								'undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent, indent,|,|,sub,sup,|,bullist,numlist,|,code,|,image',
								'theme_advanced_buttons2' => 'formatselect,styleselect,|,cut,copy,paste,pastetext,pasteword,|,search,replace, tablecontrols,|,removeformat,visualaid,|,tablecontrols',
								'theme_advanced_buttons3' => '',
								'theme_advanced_buttons4' => '',
								'theme_advanced_toolbar_location' => 'top',
								'theme_advanced_toolbar_align' => 'left',
								'theme_advanced_statusbar_location' => 'none',
								'plugins' => 'autoresize,table',		
						
					),				
		));		
	}
	/**
	 * Does not work Finder Crashes
	 * 
	 * @param type $model
	 * @param type $attribute
	 */
	public function finder($model, $attribute, $options=array())
	{
		$this->widget(
				'ext.elFinder.ServerFileInput',
				array(		
					'model' => $model,
					'attribute' => $attribute,		
					'connectorRoute' => 'elFinder/connector',		
					'settings' => $options	
				)		
		);
	}
	
	public function player($model, $attribute, $options=array(), $captureOutput=false)
	{		
		
		return $this->widget(
				'ext.circlePlayer.CirclePlayerWidget',
				array(
						'model' => $model,
						'attribute' => $attribute,
				),
				$captureOutput);
	}
	
	public function image($model, $attribute, $options=array())
	{
			$defaults = array_merge(
					array(
						'size' => 'large',	
						'filter' => 'image-large-filter',	
					),	
					$options	
				);
		$dir = YiiBase::getPathOfAlias('webroot.users.images.'.$defaults['size']);
	  $files = CFileHelper::findFiles($dir, Yii::app()->params[$defaults['filter']]);	
		$current = $model->$attribute;
		$s = '<select name="'.get_class($model).'['.$attribute.']" id="id-'.get_class($model).'">';
		foreach ($files as $file) {
			$nm = substr($file,strlen($dir) + 1);
			$s .= '<option value="'.$nm.'"'. (($file === $current) ? ' select="1"':'').'>'.CHtml::encode ($nm).'</option>';
		}
		return $s.'</select>';
	}
	
	/**
	 * returns all pagestyles
	 * @return array
	 */
	
	public function getPageStyles()
	{
		/*
		if ($this->_pageStyles === null) {
			$this->_pageStyles = new ArticleStyles($this);
		}			
		return $this->_pageStyles;
		 * 
		 */
	}
	public function setPageStyle($style)
	{
		Yii::app()->style->current = $style;
		//$this->pageStyles->pageStyle = $style;
	}
	public function getPageStyle()
	{
		return Yii::app()->style;
		// return $this->pageStyle->pageStyle;
	}
	
	public function style($element, $text = '')
	{
		//return $this->pageStyle->elements[$element];
	}
	
	/*
	public function getPageStyles()
	{
		if ($this->_pageStyles == null) {
			// must create the $model because the pageElement may use it
			$model = isset($this->model) ? $this->model : new Article();
			$this->_pageStyles = require(Yiibase::getPathOfAlias('application.views.article.pageElements').'.php');
			foreach ($this->_pageStyles['styles'] as $key => $definition) {
				if (isset($definition['default']) && $definition['default']) {
					$this->_defaultStyle = $key;
					break;
				}
			}
		}
		return $this->_pageStyles['styles'];
	}
	
	public function getPageStyle()
	{
		return $this->_pageStyle;
	}
	public function setPageStyle($style)
	{
		foreach ($this->pageStyles as $name => $styleDef) {
			if ($name == $style) {
				$this->_pageStyle = $style;
				return;
			}
		}
	}

	public function style($element, $text = '')
	{
		if (!is_array($text)) {
			$text = array('text' => $text);
		}
		
		$s = $this->pageStyles[$this->pageStyle]['elements'];
		if (isset($s[$element])) { 
			$elem = $s[$element];
			if (is_array($elem)) {
				$fields = isset($elem['fields']) ? $elem['fields'] : array();
				$template = isset($elem['template']) ? $elem['template'] : '';
				foreach ($fields as $field => $value) {
					if  ($field[0] == ':') { //(isset($this->model->$value)) {
						$template = str_replace($field, $this->model->$value, $template);
					} else {				
						$template = str_replace($field, $text[$field], $template);
					}	
				}	
				return $template;
			} else
				return $elem;
		} 
		return '';
	}
	*/
	/**
	 * retrieve the text of the wizard
	 * 
	 * @param array $key  0=>[element], 1=>[wizard]
	 * @return string
	 */
	public function wizard($key)
	{
		if (count($key) == 2) {
			$s = $this->pageStyles[$key[0]]['wizards'];
			if (isset($s[$key[1]])) {
				return $s[$key[1]];
			}
		}					
		return '';					
	}
	
	public function styleArray($part = 'script')
	{
		return $this->pageStyles->$part;
	}
	
	/**
	 * list the pagestyle to be used by an dropdown
	 * the caption property may be used the make sensible name
	 * 
	 * @return array
	 */
	public function getPageStyleOptions()
	{
		$k = array();
		foreach ($this->pageStyles as $key => $val) {
			$s = isset($val['caption']) ? $val['caption'] : $key;
			$k[$key] = $s;
			if ($this->model != null && $this->model->isNewRecord && isset($val['wizards'])) {
				foreach ($val['wizards'] as $wKey => $wVal) {
					$k[$key.'.'.$wKey] = $s.' - '.Yii::t('app', 'set content to: ').$wKey;
				}
			}	
		}	
		return $k;
	}

	
	/** from GxController
	 * 
	 * @param type $key
	 * @param type $modelClass
	 * @return type
	 * @throws CHttpException
	 */
	public function loadModel($key, $modelClass) {

		// Get the static model.
		$staticModel = GxActiveRecord::model($modelClass);

		if (is_array($key)) {
			// The key is an array.
			// Check if there are column names indexing the values in the array.
			reset($key);
			if (key($key) === 0) {
				// There are no attribute names.
				// Check if there are multiple PK values. If there's only one, start again using only the value.
				if (count($key) === 1)
					return $this->loadModel($key[0], $modelClass);

				// Now we will use the composite PK.
				// Check if the table has composite PK.
				$tablePk = $staticModel->getTableSchema()->primaryKey;
				if (!is_array($tablePk))
					throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

				// Check if there are the correct number of keys.
				if (count($key) !== count($tablePk))
					throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

				// Get an array of PK values indexed by the column names.
				$pk = $staticModel->fillPkColumnNames($key);

				// Then load the model.
				$model = $staticModel->findByPk($pk);
			} else {
				// There are attribute names.
				// Then we load the model now.
				$model = $staticModel->findByAttributes($key);
			}
		} else {
			// The key is not an array.
			// Check if the table has composite PK.
			$tablePk = $staticModel->getTableSchema()->primaryKey;
			if (is_array($tablePk)) {
				// The table has a composite PK.
				// The key must be a string to have a PK separator.
				if (!is_string($key))
					throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

				// There must be a PK separator in the key.
				if (stripos($key, GxActiveRecord::$pkSeparator) === false)
					throw new CHttpException(400, Yii::t('giix', 'Your request is invalid.'));

				// Generate an array, splitting by the separator.
				$keyValues = explode(GxActiveRecord::$pkSeparator, $key);

				// Start again using the array.
				return $this->loadModel($keyValues, $modelClass);
			} else {
				// The table has a single PK.
				// Then we load the model now.
				$model = $staticModel->findByPk($key);
			}
		}

		// Check if we have a model.
		if ($model === null)
			throw new CHttpException(404, Yii::t('giix', 'The requested page does not exist.'));

		return $model;
	}
	
	/**
	 * Loads the definition of form in the array
	 * 
	 * @param string $formName
	 * @return array
	 */
	public function loadForm($formName)
	{
		$filename = $this->viewPath($formName, array('extension' => '.php') );
		if ($filename) {
			return require(Yiibase::getPathOfAlias('application').'/'.$filename );
			// return require(Yiibase::getPathOfAlias('application.views.'.$this->id.".$formName").'.php');		
		}	
		return false;
	}
	
	public function createAction($actionID) {		
		Yii::app()->onEndRequest = array($this, 'writeTime');		
	  return parent::createAction($actionID);
	}
	
	public function writeTime()
	{
		Yii::app()->pageLog->writeExecutionTime($this->logPageSpeed, $this);
	}	
	
	public function getLogPage()
	{
		return Yii::app()->pageLog->log->write;
	}
	public function setLogPage($value)
	{
		Yii::app()->pageLog->log->write = $value;
	}
	
	
	/**
	 * Looking for the in:
	 *   current [view] directory
	 *   in project: current layout directory
	 *   in vendor/toxus/views/[view]/ directory
	 *   in vendor/toxus/layout directory
	 * 
	 * 
	 * @param string $filename the name of the file to find EXCLUDING twig
	 * @return string
	 */
	public function viewPath($filename, $options = array())
	{
		if ($options['extension']) {
			$ext = $options['extension'];
		} else {	
			$ext = Yii::app()->viewRenderer->fileExtension;
		} 
		$path = YiiBase::getPathOfAlias('webroot.protected.views');
		if (file_exists($path .'/'.$this->getId().'/'.$filename.$ext)) {
			return 'views/'.$this->getId().'/'.$filename.$ext;
		} elseif (file_exists($path .'/layouts/'.$filename.$ext)) {
			return 'views/layouts/'.$filename.$ext;
		} else {
			$path = YiiBase::getPathOfAlias('webroot.protected.'.$this->vendorViewRoot);
			if (file_exists($path .'/'.$this->getId().'/'.$filename.$ext)) {
				return str_replace('.', '/', $this->vendorViewRoot).'/'.$this->getId().'/'.$filename.$ext;
			} elseif (file_exists($path .'/layouts/'.$filename.$ext)) {
				return str_replace('.', '/', $this->vendorViewRoot).'/layouts/'.$filename.$ext;
			} else {			
				return false;
			}	
		}		
	}

	public function resolveViewFile($viewName,$viewPath,$basePath,$moduleViewPath=null)
	{
	  $path = YiiBase::getPathOfAlias('webroot.protected.'.$this->vendorViewRoot.'.'.$this->getId());
		$s = parent::resolveViewFile($viewName, $viewPath, $basePath, $path);
		if ($s === false) {
			$s = parent::resolveViewFile('/'.$viewName, $viewPath, $basePath, $path);
			if ($s === false) {
				$s = parent::resolveViewFile('//'.$viewName, $viewPath, $basePath, $path);
				if ($s == false) {
					$path = YiiBase::getPathOfAlias('webroot.protected.'.$this->vendorViewRoot.'.layouts');					
					$s = parent::resolveViewFile('/'.$viewName, $viewPath, $basePath, $path);					
					if ($s == false) {
						$path = YiiBase::getPathOfAlias('webroot.protected.'.$this->vendorViewRoot.'.layouts');					
						$s = parent::resolveViewFile('//'.$viewName, $viewPath, $basePath, $path);											
					}
				} 
			}
		}
		return $s;
	}
	
}