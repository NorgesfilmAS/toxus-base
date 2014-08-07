<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 * 
 * if logPage = false no logging is done for the request, otherwise info is written
 * 
 */

// YiiBase::setPathOfAlias('assetsBase', realpath(dirname(dirname(__FILE__)).'/../assetsBase'));

class BaseController extends CController
{
	public $vendorViewRoot = 'toxus.views';
	public $layout = false;////layouts/column1';
	//public $breadcrumbs=array();
	public $brand = 'BaseController->brand';
	public $model = null;
	protected $_logPageView = true;
	public $_menu = null;
	
  /**
   * the name of the tooltip file placed in the message directory
   * @var string
   */
  public $toolTipFilename = 'tooltip';
  
	public $_pageStyles = null;	
	public $_defaultStyle = '';
	public $_pageStyle = 'default'; // should come from $model->page_style	
	public $logPageSpeed = true;    // if true the page generation speed is stored 
	public $logPage = true;
	
	private $_formElements;
	protected $_assetBaseUrl;

	protected $_packages = array()	; // key => array('assetUrl', 'executeAfterLoad')
	
	protected $_onReadyScript = array();  // the lines in the onReadyScript

	private $_newId = null;
	
	public function getVersion()
	{
		return '05';	// vendor:toxus: 0.2
	}

	public function getForm()
	{
		if ($this->_formElements == null) {
			$this->_formElements = new FormElements();
		}
		return $this->_formElements;
	}
	
	/**
	 * 
	 * @param string $type controller
	 */
	protected function loadMenu($controllerName)
	{
		Yii::log('Loading menu', CLogger::LEVEL_INFO);
		
		$menuDef =  array(
			'item'=> array(),    // on item page left (in affix )
			'system' => array(), // top left  (in static menu bar)
			'user'=> array(),    // top right (in static menu bar)
			'logo' => array(),   // below top menu left aligned	(in page bar)
			'header'=> array(),  // below top menu right aligned (in page bar)	
			'main'=> array(),    
//			'toolbar'=> array(),
//			'explain'=> array(),
			'footer'=> array(),
//			'popup'=> array(),
//			'help'=> array(),
		);

		/**
		 * load the system wide menu's
		 */
		foreach ($menuDef as $name => $menu) {
			$eventName = 'on'.ucfirst($name).'Menu';
	//		Yii::app()->config->connectEvents($this, $eventName);

			$menuFilename = Yii::getPathOfAlias('application.views.'.$controllerName.'.'.$name.'Menu').'.php';
			if (!file_exists($menuFilename)) {
				$menuFilename = Yii::getPathOfAlias('application.views.layouts.'.$name.'Menu').'.php';
				if (!file_exists($menuFilename)) {
					$menuFilename = Yii::getPathOfAlias('toxus.views.'.$controllerName.'.'.$name.'Menu').'.php';	
					if (!file_exists($menuFilename)) {
						$menuFilename = null;
					}	
				}
			}
			if (!empty($menuFilename)) {
				$menu = require($menuFilename);
			}	

			$event = new CMenuEvent($this);
			$event->menu = $menu;
			$this->$eventName($event);
			$menuDef[$name] = $event->menu;
		}	
		return $menuDef;
	}
	
	public function init()
	{
		/**
		 * if output is buffer, the information is send to firePHP if that is configured in the main.php
		 */
		if (isset(Yii::app()->params['firePHP']) && Yii::app()->params['firePHP'] == 1) {
			ob_start();
		}	
	}
	
	/**
	 * load all menu's of the system and connects external menu's
	 * 
	 * $type = the $this->id if null otherwise the name of the controller
	 * 
	 * @return array
	 */	
	protected function getMenu($type=null)
	{
		if ($type !== null) 
			return $this->loadMenu ($type);
		if ( empty($this->_menu)) {
			/*
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
			
			foreach ($this->_menu as $name => $menu) {
				$eventName = 'on'.ucfirst($name).'Menu';
				Yii::app()->config->connectEvents($this, $eventName);

				$menuFilename = Yii::getPathOfAlias('application.views.'.$type.'.'.$name.'Menu').'.php';
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
				*/			
			$this->_menu = $this->loadMenu($this->id);						
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
	public function onLogoMenu($event)
	{
		$this->raiseEvent('onLogoMenu', $event);		
	}
	public function onHeaderMenu($event)
	{
		$this->raiseEvent('onHeaderMenu', $event);		
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
	 * 
	 * @param array $menu the menu definition
	 * @param string $menuDef the type of menu generated
	 * @param array $options options for the menu 
	 */
	protected function beforeMenuGenerated($menu, $name, $options)
	{
		return $menu;
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
// pre version it was:	public function menuHtml($menuDef, $controller = null)
	public function menuHtml($menuDef, $options = array())
	{	
		if (is_array($menuDef)) {
			$menuName = isset($menuDef['name']) ? $menuDef['name'] : 'default';
			$menu = array($menuName => isset($menuDef['menu']) ? $menuDef['menu'] : array());
		} else {
			$menuName = $menuDef;
			$menu = $this->getMenu(); //$menu = $this->getMenu($controller);
		}	
		
		if (!isset($menu[$menuName])) return '';
		$m = $this->beforeMenuGenerated($menu[$menuName], $menuDef, $options);
		$params = array(
			'menu' => $m, // $menu[$menuName],	
			'name' => $menuName,	
		);
		if (isset($options['class']))
			$params['layout'] = array('class'=>$options['class']);
		// $this->beforeMenuGenerated(&$menu, $menuDef, $options);
		$path = $this->viewPath('_'.$menuName.'Menu',array('return' => true));
		if (!$path) {
			$path = $this->viewPath('_menu',array('return' => true, 'noExtension' => true));
			if ($path) {
				$s = $this->renderPartial('_menu', $params, true);
			}
		} else {
			$s = $this->renderPartial('_'.$menuName.'Menu', $params, true);
		}
		return $s;
	}


	public function renderAjax($view, $data = null) {
		$this->renderPartial($view, $data, false, true);
	}
	public function ajaxEnd($result = 'ok')
	{
		echo $result;
		Yii::app()->end(200);
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
					'label' => Yii::t('button', 'menu-sign-up'),
					'url' => $this->createUrl('login/new'),
					'icon' => 'icon-user',	
				),
				'sign-in' => array (
					'label' => Yii::t('button', 'menu-sign-in'),
					'url' => $this->createUrl('login/index'),						
				),	
			);
		} else {
			$menu = array(
				'sign-out' => array (
					'label' => Yii::t('button', 'menu-sign-out'),
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
	 *bootstrap.css
	 */	
	public function findPackage($name)
	{
		$packages = array(
			'bootstrap' => array(
				'basePath' => 'toxus.assetsBase.crisp',
				'css' => array(
						'css/bootstrap.css', 
						'css/bootstrap-responsive.css',
						'css/font-awesome.min.css',
						'css/responsive-tables.css'),
				'js' => array(
					CClientScript::POS_HEAD => array(
										'js/modernizr.custom.87724.js'
					),											
					CClientScript::POS_END => array(
										'js/bootstrap.min.js'
					),
				),
			),	
			'bootstrap3' =>array(
				'basePath' => 'toxus.assetsBase.bootstrap302.dist', //'toxus.assetsBase.bootstrap3',
				'css' => array(
						'css/bootstrap.css', 
				),		
				'js' => array(
					CClientScript::POS_END => array(
						'js/bootstrap.js',
					),
				),					
			),	
			'glyphicons' => array(
				'basePath' => 'toxus.assetsBase.glyphicons.dist', //'toxus.assetsBase.bootstrap3',
				'css' => array(
						'css/bootstrap-glyphicons.css',
				),							
			),	
			'crisp' => array(
				'basePath' => 'toxus.assetsBase.crisp',
				'css' => array(
						'css/style.css', 
						'css/header-1.css', 
						'css/toxus.css'
				),
				'js' => array(
					CClientScript::POS_END => array(
								'js/jquery.dcjqaccordion.2.7.min.js', 
						//		'js/ddsmoothmenu-min.js',
								'js/scripts.js', 
								'js/respond.min.js'
					),
				),
			),
			'core' => array(
				'basePath' => 'toxus.assetsBase.core',		
				'js' => array(	
					CClientScript::POS_HEAD => array(
//										'js/modernizr.custom.87724.js'
						'js/modernizr.js'
					),																	
					CClientScript::POS_END => array(
							'js/respond.min.js',
							'js/application.js',							
							'js/core.js', 
							'js/toolbox.date.js',
					),		
				),
				'css' => array(
					'css/application.css',
				)	
			),	
			'smooth' => array(
				'basePath' => 'toxus.assetsBase.crisp',				
				'js' => array(
					CClientScript::POS_END => array(
								'js/ddsmoothmenu-min.js',
								'js/ddsmooth-init.js',
					),
				),					
			),	
				
			'typeahead' => array(
				'basePath' => 'toxus.assetsBase.typeahead2',
				'js' => array(
					CClientScript::POS_END => array(
						'dist/typeahead.bundle.js',
					),						
				),	
			),	
			'ajaxForm' => array(
				'basePath' => 'toxus.assetsBase.jquery-form',
				'js' => array(
					CClientScript::POS_END => array(
						'js/jquery.form.js',
					),	
				),	
			),	
			'datetimepicker' => array(
				'basePath' => 'toxus.assetsBase.crisp',
				'css' => array(
					'css/datetimepicker.css',	
				),	
				'js' => array(
					CClientScript::POS_END => array(
						'js/bootstrap-datetimepicker.js',
					),	
						
				),						
			),
			'datepicker' => array(
				'basePath' => 'toxus.assetsBase.bootstrap-datepicker',
				'css' => array(
					'css/datepicker.css',
					'css/timepicker.css',	
				),	
				'js' => array(
					CClientScript::POS_END => array(
						'js/bootstrap-datepicker.js',
						'js/bootstrap-timepicker.js',	
					),	
				),											
			),
			'currency' => array(
				'basePath' => 'toxus.assetsBase.jquery-maskmoney',					
				'js' => array(
					CClientScript::POS_END => array(
						'js/jquery.maskMoney.js',
					)		
				),
				'ready' => '$(".input-currency").maskMoney('.Util::param('currencyFormat', '{thousands:".", decimal:","}').')',				
			),
			'inputmask' => array(
				'basePath' => 'toxus.assetsBase.jquery-inputmask',					
				'js' => array(
					CClientScript::POS_END => array(
						'js/jquery.inputmask.js',
					)		
				),
				// 'ready' => '$(".input-mask").maskMoney('.Util::param('currencyFormat', '{thousands:".", decimal:","}').')',				
					
			)	,
			'vat' => array(
				'basePath' => 'toxus.assetsBase.crisp',
				'js' => array(
					CClientScript::POS_END => array(
						'js/customComboBox.js'
					),	
				),	
			),	
			'chosen' => array( /* version 1.0 */
				'basePath' => 'toxus.assetsBase.chosen',
				'js' => array(
					CClientScript::POS_END => array(
						'chosen.jquery.js'
					),	
				),						
				'css' => array(
					'chosen.ext.css',	
				),	
				'ready' => '$(".chosen-select").chosen({ allow_single_deselect:true }); $(".chosen-container").addClass("form-control");',									
			),
			'tinymce' => array(
				'basePath' => 'toxus.assetsBase.tinymce',
				'js' => array(
					CClientScript::POS_END => array(
						'tinymce.min.js',
						'tinymce.jquery.min.js'	
					),	
				),						
				'ready' => 
					'tinymce.init({
						selector: ".tinymce",
 					  entity_encoding : "named",

						menubar: "edit format insert table tools view",
						relative_urls: false,
						plugins: [
							"autolink lists link charmap ",
							"searchreplace visualblocks code fullscreen",
							"insertdatetime table contextmenu paste autoresize"
						],
						toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link insertdate "
					});',									
			),
			'code' => array(
				'basePath' => 'toxus.assetsBase.codemirror',
				'js' => array(
					CClientScript::POS_END => array(
						'lib/codemirror.js',
						'jquery-codemirror/jquery.codemirror.js',	
						'mode/htmlmixed/htmlmixed.js'	
					),	
				),						
				'css' => array(
					'lib/codemirror.css',	
				),
				'ready' => '
					$(".code-html").codemirror({ mode: "htmlmixed", lineNumbers: true, viewportMargin: Infinity});					
					$(".code-javascript").codemirror({mode: "javascript", lineNumber: true,viewportMargin: Infinity });
				'	
			),	
			'code-html' => array(
				'basePath' => 'toxus.assetsBase.codemirror.mode',	
				'js' => array(
					CClientScript::POS_END => array(
							'xml/xml.js',
							'javascript/javascript.js',
							'css/css.js',    
							'htmlmixed/htmlmixed.js'
					),	
				),						
			),
			'modal-dialog' => array(
				'ready' => '
						$(".menu-modal").on("click", function() {
							div = $(this).data("div");
							if (div) {
								$(div + " .modal-content").html($("#id-wait-message").html());
								$(div).modal("show"); 							
								$(div + " .modal-content").load($(this).data("url"));
							} else {
								$("#id-modal-body").html($("#id-wait-message").html());
								$("#id-modal").modal("show"); 							
								$("#id-modal-body").load($(this).data("url"));							
							}
					})'
			),
			'elastic' => array(
				/* auto expand an textarea to the number of lines used */	
				'basePath' => 'toxus.assetsBase.jquery-elastic',	
				'js' => array(
					CClientScript::POS_END => array(
							'jquery.elastic.source.js',
					),
				),
				'ready' => '$("textarea").elastic()',	
			),
			'dropzone'=> array(
				/* drag drop file upload */	
				'basePath' => 'toxus.assetsBase.dropzone',	
				'js' => array(
					CClientScript::POS_END => array(
							'dropzone.js',
					),
				),	
				'css' => array(
					'css/dropzone.css',	
				),					
			),	
			'jwplayer' => array(
				/* auto expand an textarea to the number of lines used */	
				'basePath' => 'toxus.assetsBase.jwplayer',	
				'js' => array(
					CClientScript::POS_END => array(
							'jwplayer.js',
					),
				),
				'ready' => 'jwplayer.key="0V3895lP4LH4KDl6jlC9NQ5mtM6YVhUZP9aURA=="',						
			),
			'videojs' => array(
				'basePath' => 'toxus.assetsBase.videojs.src',
				'css' => array(
					'cdn' => '//vjs.zencdn.net/4.2/video-js.css',	
				),
				'js' => array(
					CClientScript::POS_END => array(
						'cdn' => '//vjs.zencdn.net/4.2/video.js'
					),	
				)	
			),
			'sortable' => array(
				'basePath' => 'toxus.assetsBase.jquery-sortable.source',
				'css' => array(
					'css/coderay.css',
				),
				'js' => array(
					CClientScript::POS_END => array(
						'js/jquery-sortable-min.js',
					),	
				),
			),
			'bootstrap-switch' => array(
				'basePath' => 'toxus.assetsBase.bootstrap-switch.dist',
				'css' => array(
					'css/bootstrap3/bootstrap-switch.css',
				),
				'js' => array(
					CClientScript::POS_END => array(
						'js/bootstrap-switch.js',
					),	
				),
			),				
			'ajaxform' => array(
				'basePath' => 'toxus.assetsBase.ajaxForm',
				'js' => array(
					CClientScript::POS_END => array(
						'jquery.form.js',
					),	
				),
			),				
				
			'new' => array(
				'basePath' => 'alias',
				'css' => array(),
				'js' => array(
					CClientScript::POS_BEGIN => array(),
					CClientScript::POS_HEAD => array(),
					CClientScript::POS_END => array(),
					CClientScript::POS_LOAD => array(),
					CClientScript::POS_READY => array(),	
				),
			),
		);
		if (isset($packages[$name]))
			return $packages[$name];
		return null;
	}
	
	
	public function getReady($name)
	{
		$package = $this->findPackage($name);
		return isset($package) && isset($package['ready']) ? $package['ready'] : '';
	}
	/**
	 * register a package and returns the url to the assets dir
	 * 
	 * 
	 * @param string $name
	 * @param array $options
	 *			executeScriptAfterLoad: execute the script after the package is loaded. Can be used in Ajax calls. After this script the onReady is executed
	 *					if it's an array (key => value) key is the unique name, value is the script. (to stop duplicate loading of the code)
	 *			isAjax: if true the packages are NOT register to auto load but are stored so the scriptOnReady will generate the load on demande
	 */
	public function registerPackage($name, $options = array())
	{
		$opt = array_merge( 
			array(				
				'isAjax' => false,	
				'executeAfterLoad' => null,					
			),
			$options);
		
		if (!isset($this->_packages[$name])) {// test if it already registered
			$package = $this->findPackage($name);
			if ($opt['isAjax'] == false && isset($package)) { // do not load in the ajax version
				if (isset($package['basePath'])) {
					$assetUrl = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($package['basePath']));
					if (!$opt['isAjax'] && isset($package['css']))
						foreach ($package['css'] as $tx => $css) {
							Yii::app()->clientScript->registerCSSFile((($tx === 'cdn') ? '' : ($assetUrl.'/')).$css);					
						}
					if (!$opt['isAjax'] &&  isset($package['js'])){	
						foreach ($package['js'] as $position => $scripts) {
							foreach ($scripts as $type => $script) {
								Yii::app()->clientScript->registerScriptFile( ($type === 'cdn' ? '' : ($assetUrl.'/')).$script, $position);
							}
						}
					}	
				} else {
					$assetUrl = '';
				}		
				if (isset($package['ready'])) {
					Yii::log("Registering package $name, with onReady", CLogger::LEVEL_TRACE, 'toxus.compontents.BaseController');
					$this->registerOnReady($package['ready']);
				}	else {
					Yii::log("Registering package $name, with no onReady", CLogger::LEVEL_TRACE, 'toxus.compontents.BaseController');
				}
				$this->_packages[$name] = array('assetUrl' => $assetUrl);	// has been registered
			}
		}
		// even if the package is already loaded, the script must be executed if not yet found
		if (isset($opt['executeAfterLoad'])) {
			Yii::log("Binding package executeAfterLoad for $name", CLogger::LEVEL_TRACE, 'toxus.compontents.BaseController');

			// get the name of the script if it has one
			$script = is_array($opt['executeAfterLoad']) ? $opt['executeAfterLoad'] : array(md5($opt['executeAfterLoad']) => $opt['executeAfterLoad']);
			// check if there are scripts already
			if (!isset($this->_packages[$name]['executeAfterLoad'])) { // first script to load
				$this->_packages[$name]['executeAfterLoad'] = $script;
			} elseif (!isset($this->_packages[$name]['executeAfterLoad'][key($script)])) {
				// script not found: add to the end
				$this->_packages[$name]['executeAfterLoad'][key($script)] = $script[key($script)];
			}
		}	
	}
	
	/**
	 * return the script associated with the package so we can load them 
	 * in an ajax call: NOT NEEDED ANY MORE: use registerPackage('name', array('active' => 
	 * 
	 * @param string $name the package to get the scripts from
	 */
	public function packageScripts($name)
	{
		$result = array();
		$package = $this->findPackage($name);
		if (isset($package)) {		
			if (isset($package['basePath'])) {
				$assetUrl = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($package['basePath']));
				if (isset($package['js'])){	
					foreach ($package['js'] as $scripts) {
						foreach ($scripts as $script) {
							$result[] = $assetUrl.'/'.$script;
						}
					}
				}	
			}
		}
		return $result;
	}
	
	/**
	 * 
	 * @param string $name the name of the package
	 * @return string ore null if not found
	 */
	public function getPackageBaseUrl($name)
	{
		if (isset($this->_packages[$name]))
			return $this->_packages[$name]['assetUrl'];
		$package = $this->findPackage($name);
		if (isset($package)) {
			if (isset($package['basePath'])) {
				return  Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($package['basePath']));
			}
		}	
		return null;
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
	
	public function registerCore($part, $force = false)
	{
		Yii::app()->clientScript->registerCoreScript($part, $force);
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
	/**
	 * add a script to the onReady statement
	 * NOT USED ANY MORE
	 * @param string $script
	 */
	public function registerOnReady($line)
	{
		$this->_onReadyScript[] = $line;
		return '';
	}
	/**
	 * add a script to the onReady event 
	 * 
	 * @param string $script the script to execute in the onReady event
	 * @param array $options
	 *		name: if given this must be unique in the onReady 
	 * 
	 * @return boolean true if script is added
	 */
	public function onReady($script, $options = array())
	{
		if (isset($options['name'])) {
			if (isset($this->_onReadyScript[$options['name']])) {
				return false;
			} else {
				$this->_onReadyScript[$options['name']] = $script;
			}						
		} else {
			$this->_onReadyScript[] = $script;
		}	
		return '';		
	}
	
	/**
	 * generate the onReady scription
	 * 
	 * @param bool $isAjax if true the pages are loaded through $.getScript and the code is executed when
	 *   the package is loaded
	 *   WARNING: css can't be loaded this way
	 */
	public function scriptOnReady($isAjax = false)
	{
		$script = '';		
		if ($isAjax) {
			// load the script and css dynamic and wait for the packages to load before executing scripts
			foreach ($this->_packages as $name => $packageOptions) {
				$package = $this->findPackage($name);
				if (!$package) {
					$script .= "/* package $name NOT FOUND */\n";
				} else {
					$script .= "/* package $name */\n\t";
					if (isset($package['basePath'])) {
						$assetUrl = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($package['basePath']));
						if (isset($package['css'])) {
							foreach ($package['css'] as $css) {
								$script .= '$("<link/>", {'.
									'rel: "stylesheet",'.
									'type: "text/css",'.
									'href: "'.$assetUrl.'/'.$css.'"'.
									'}).appendTo("head");';
							}
						}	
					}	
					if (isset($package['js'])){	
						foreach ($package['js'] as $scripts) {
							$l = 1;
							foreach ($scripts as $source) {
								$script .= '$.getScript("'.$assetUrl.'/'.$source.'"';
								if ($l == count($source) && isset($packageOptions['executeAfterLoad'])) {
									$script .= ', function(data, textState) {';
									foreach ($packageOptions['executeAfterLoad'] as $optName => $optSource) {
										$script .= "\n\t\t/* script: $optName */\n\t\t";
										if (is_array($optSource)) {
											foreach ($optSource as $s)
												$script .= $s;
										}	else {
											$script .= $optSource;
										}
									}
									if (isset($package['ready'])) {
										$script .= "\n\t\t".$package['ready']."\n";
									}
									$script .= "\n\t});\n";
								} elseif ($l == count($source) && isset($package['ready'])) {
									$script .= "/* script: package.ready */\n\t";
									$script .= ', function(data, textState) {';
									$script .= $package['ready'];
								  $script .= "});\n";
								} else	{
									$script .= "\n\t);\n";
								}	
								$l++;
							}
						}
					}	else {
						if (isset($package['ready'])) {
							$this->registerOnReady($package['ready']);  // SHOULD THIS NOT BE scriptOnReady (can be but is same!)
						}	
					}	
				}
			}
			foreach ($this->_onReadyScript as $scriptLine) {
				$script .= "\t\t".$scriptLine."\n";
			}				
		} else {			
			Yii::log('non Ajax.onReady number of scripts = '.count($this->_onReadyScript), CLogger::LEVEL_TRACE, 'toxus.components.BaseController');
			foreach ($this->_onReadyScript as $scriptLine) {
				$script .= "\t\t".$scriptLine."\n";
			}	
			// check for any ExecuteAfterLoad
			foreach ($this->_packages as $name => $packageOptions) {
				if (isset($packageOptions['executeAfterLoad']) && is_array($packageOptions['executeAfterLoad'])) {
					foreach ($packageOptions['executeAfterLoad'] as $optName => $optSource) {
						$script .= "/* script: $name/$optName */\n";
						$script .= $optSource;
					}	
				}	
			}
		}	
		return $script;
	}
	
	public function addHeader($header='X-UA-Compatible: IE=edge,chrome=1')
	{				
		header($header);		
	}
	
	/**
	 * translate a message
	 * DO NOT USE: User Yii_t(...)
	 * 
	 * @param string $msg
	 * @param array $params
	 */
	public function t($msg, $params=array(), $return = false)
	{
		if (is_string($msg)) {
			$m = ucfirst(Yii::t('base', $msg, $params));		
			if (! is_array($params) || $return )
			  return $m;	
			else {
			  echo $m; 
			}
		}
	}
	/** 
	 * 
	 * DO NOT USE: User Yii_t(...)
	 */
	public function te($msg, $params=array())
	{
		if (is_string($msg)) {
			return ucfirst(Yii::t('base', $msg, $params));
		}	
	}
	
	public function flash()
	{	
		$flashMessages = Yii::app()->user->getFlashes();
		if ($flashMessages) {
			$this->render('//layouts/_flash', array('flashes' => $flashMessages));
		}				
	}
	
	public function formAdjust(&$form, $isNew = true)
	{
		if ($isNew) {
			$form['title'] = Yii::t('base','new',1).' '.$form['title'];
			$form['buttons']['submit']['label'] = Yii::t('base','create', 1);
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
		$this->widget('toxus.extensions.tinymce.TinyMce', array(
				'model' => $model,
				'attribute' => $attribute,
				// Optional config
				'compressorRoute' => false, //'tinyMce/compressor',
				//'spellcheckerUrl' => array('tinyMce/spellchecker'),
				// or use yandex spell: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
				'spellcheckerUrl' => 'http://speller.yandex.net/services/tinyspell',
				'fileManager' => array(
						'class' => 'toxus.extensions.elFinder.TinyMceElFinder',
						'connectorRoute'=>'elFinder/connector',
				),
				'htmlOptions' => array(
						'rows' => 6,
						'cols' => 100,
						'class' => 'span10 col-lg-10',
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
					$k[$key.'.'.$wKey] = $s.' - '.Yii::t('base', 'set content to: {key}', array('{key}' => $wKey ));
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
					throw new CHttpException(400, Yii::t('base', 'Your request is invalid.'));

				// Check if there are the correct number of keys.
				if (count($key) !== count($tablePk))
					throw new CHttpException(400, Yii::t('base', 'Your request is invalid.'));

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
					throw new CHttpException(400, Yii::t('base', 'Your request is invalid.'));

				// There must be a PK separator in the key.
				if (stripos($key, GxActiveRecord::$pkSeparator) === false)
					throw new CHttpException(400, Yii::t('base', 'Your request is invalid.'));

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
			throw new CHttpException(404, Yii::t('base', 'The requested page does not exist.'));

		return $model;
	}
	
	/**
	 * Parses a form for special tokens like buttons
	 * @param array $form
	 */
	public function parseForm($form)
	{
		if (isset($form['buttons']) && is_string($form['buttons'])) {
			$form['_buttons'] = $form['buttons']; // remember the state
			switch ($form['buttons']) {
				case 'default' : $form['buttons'] =  
					array(
					'view' => array(
						'edit' => $this->button(array(
							'label' => 'btn-edit',
							'url' => '?mode=edit',
							'position' => 'pull-right',						
						)),	
					),
					'edit' => array(
						$this->button('cancel'),				
						$this->button('submit'),	
					),	
				);			
				break;
			default :
				Yii::log('Unknown button type in load form: '.$form['buttons'].'. Expection: default');					
			}
		}
		return $form;		
	}
	
	/**
	 * Loads the definition of form in the array
	 * 
	 * @param string $formName
	 * @return array
	 */
	public function loadForm($formName = 'formFields')
	{
		$filename = $this->viewPath($formName, array('extension' => '.php') );
		if ($filename) {
			$form = require(Yiibase::getPathOfAlias('application').'/'.$filename );
			// for loading default buttons
			return $this->parseForm($form);
		}	
		return false;
	}
	
	public function createAction($actionID) {		
		Yii::app()->onEndRequest = array($this, 'writeTime');		
	  return parent::createAction($actionID);
	}
	
	public function writeTime()
	{
		if ($this->logPage) {
			Yii::app()->pageLog->writeExecutionTime($this->logPageSpeed, $this);
		}	
	}	
	
	public function getLogPage()
	{
		return Yii::app()->pageLog->log->write;
	}
	public function setLogPage($value)
	{
		if ($this->logPage) {
			Yii::app()->pageLog->log->write = $value;
		}	
	}
	
	/**
	 * finds the file in the array of paths
	 * if file not find false is returned
	 * 
	 * @param string $filename
	 * @param array $paths
	 */
	public function pathSearch($filename, $paths)
	{
		foreach ($paths as $key => $path) {
			if (file_exists($path.'/'.$filename)) {
				return $key.'/'.$filename;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param string $filename name without the extension
	 * @param array $options defined:
	 *   'extension' => use this extension for the file
	 *   'libOnly' => will only search the vendor definitions
	 *   'return' => if 1 the no error will be thrown, but false is returned
	 *   'noExtension' => return the filename without the extension
	 *   'directory' => the directory to search in default: class name
	 * 
	 * @return boolean / string the path to the file relative to application
	 * @throws CException
	 */
	public function viewPath($filename, $options = array())
	{
		$extension = (isset($options['extension']) ? $options['extension'] : Yii::app()->viewRenderer->fileExtension);
		$classname = isset($options['directory']) ? $options['directory'] : $this->getId();
		$filename = $filename.$extension;
		$vendorRoot = YiiBase::getPathOfAlias($this->vendorViewRoot);
		$app = Yii::getPathOfAlias('application');
		//		$shortVendorRoot = str_replace('.', '/', $this->vendorViewRoot);
		$shortVendorRoot = substr($vendorRoot, strlen($app) + 1);
		
		$paths = array();
		if (substr($filename,0,1) == '/') { // looking in a fixed directory /test/index
			$p = explode('/', $filename);
			$paths = array('views/'.$p[1] => Yii::getPathOfAlias('webroot.protected.views').'/'.$p[1]);
			$filename = $p[2];
		} 
		if (!(isset($options['libOnly']) && $options['libOnly'])) {
			$paths = $paths + array(
				'views/'.$classname => YiiBase::getPathOfAlias('webroot.protected.views').'/'.$classname,
				'views/layouts' => YiiBase::getPathOfAlias('webroot.protected.views').'/layouts',	
			);		
		}
		$paths[$shortVendorRoot.'/'.$classname] = $vendorRoot.'/'.$classname;
		$paths[$shortVendorRoot.'/layouts'] = $vendorRoot.'/layouts';
		if (($file = $this->pathSearch($filename, $paths)) == false) {
			if (isset($options['return']) && $options['return']) {
				return false;
			}
			throw new CException('The template "'.$filename.'" could not be found');
		}
		if (isset($options['noExtension']) && $options['noExtension']) {
			$file = substr($file, 0, - strlen($extension));
		}
		return $file;		
	}
	/**
	 * Looking for the in:
	 *   current [view] directory
	 *   in project: current layout directory
	 *   in vendor/toxus/views/[view]/ directory
	 *   in vendor/toxus/layout directory
	 * 
	 *  
	 * 
	 * @param string $filename the name of the file to find EXCLUDING twig
	 * @param array $options
	 *    extension string : the extension to use, default ''
	 *    parent boolean: find the parent of this file, default false;
	 * @return string
	 */
	public function viewPath2($filename, $options = array())
	{
		if (isset($options['extension'])) {
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
			$path = YiiBase::getPathOfAlias($this->vendorViewRoot);
			if (file_exists($path .'/'.$this->getId().'/'.$filename.$ext)) {
				return str_replace('.', '/', $this->vendorViewRoot).'/'.$this->getId().'/'.$filename.$ext;
			} elseif (file_exists($path .'/layouts/'.$filename.$ext)) {
				return str_replace('.', '/', $this->vendorViewRoot).'/layouts/'.$filename.$ext;
			} else {
				throw new CException('The template "'.$filename.'" could not be found');
			//	return false;
			}	
		}		
	}

	/**
	 * Overloaded Yii version	 
	 * 
	 * 
	 */
	public function resolveViewFile($viewName,$viewPath,$basePath,$moduleViewPath=null)
	{
		$s = $this->viewPath(($viewName));
		if ($s) {
			return YiiBase::getPathOfAlias('application').'/'.$s;
		}
		return $s;
		/**
		 * don't get it, but should look the same way viewPath is looking for the file
		 */
	  $path = YiiBase::getPathOfAlias($this->vendorViewRoot.'.'.$this->getId());
		$s = parent::resolveViewFile($viewName, $viewPath, $basePath, $path);
		if ($s === false) {
			$s = parent::resolveViewFile('/'.$viewName, $viewPath, $basePath, $path);
			if ($s === false) {
				$s = parent::resolveViewFile('//'.$viewName, $viewPath, $basePath, $path);
				if ($s == false) {
					$path = YiiBase::getPathOfAlias($this->vendorViewRoot.'.layouts');				
					$s = parent::resolveViewFile('/'.$viewName, $viewPath, $basePath, $path);					
					if ($s == false) {
						$path = YiiBase::getPathOfAlias($this->vendorViewRoot.'.layouts');			// has double //		
						$s = parent::resolveViewFile('//'.$viewName, $viewPath, $basePath, $path);											
						if ($s == false) {
							$s = $this->viewPath ($viewName);
							if ($s) {
								$s = Yii::getPathOfAlias('application').'/'.$s;
							}
						}	
					}
				} 
			}
		}
		return $s;
	}
	
	
	/**
	 * 
	 * @param string $$baseName the base class of the view = the most left column
	 * @param integer $id the id of the baseclass record
	 * @return class of the definition
	 */
	public function subFrameDefinition($baseName, $id)
	{
		return new SubFrameDefinition();
	}

	protected function exceptionToError($model, $e)
	{
		if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {	// duplicate
			$model->addError('id', Yii::t('base', 'This information already exists'));
		} else {
			$model->addError('id', Yii::t('base', 'There was an error saving the information. Please try again.<br /> {msg}',array('{msg}' => $e->getMessage())));
		}	
	}
	
	/**
	 * update the information in the database.
	 * $_POST[$this->controller-id] is set
	 * $this->model is the active model
	 * 
	 * @return boolean true: information save, false : redisplay form
	 */
	public function executeUpdate()
	{
		$controllerId = get_class($this->model);
		$this->model->attributes = $_POST[ucFirst($controllerId)];
		if ($this->model->validate()) {
			try {
				if ($this->model->save()){
					Yii::app()->user->lastInsertid = $this->model->id;
					return true;
				}	
			} catch(Exception $e) {
				$this->exceptionToError($this->model, $e);				
			}	
		}		
		return false;
	}
	
	/**
	 * 
	 * @return false 
	 */
	public function executeCreate($options = array())
	{
		$controllerId = isset($options['model']) ? $options['model'] : get_class($this->model);
		$this->model->attributes = $_POST[$controllerId];
		if ($this->model->validate()) {
			try {
				if ($this->model->save()) {
					Yii::app()->user->lastInsertid = $this->model->id;
					return true;
				}	
			} catch (Exception $e) {
				$this->exceptionToError($this->model, $e);
			}	
		}		
		return false;		
	}
	
	public function executeDelete()
	{
		$this->model->delete();
		return true;
	}
	
	public function field($label, $value, $options=array())
	{
		$defaults = array_merge(
						array(
								'label-start' => '<span class="info-label">',
								'label-end' => '</span> ',
								'field-start' => '',
								'field-end' => '',
								'data-start' => '',
								'data-end' =>'',
								
						), $options);
		if (!empty($value)) {
			return $defaults['data-start'].$defaults['label-start'].$label.$defaults['label-end'].$defaults['field-start'].CHtml::encode($value).$defaults['field-end'].$defaults['data-end'];
		}
	}
	
  public function hasTooltip($attributeName)
  {
    $msg = Yii::t('base', $attributeName);
    return $msg != $attributeName;
  }
  public function tooltip($attributeName, $params=array())
  {
    return Yii::t('base', $attributeName,$params);
  }
	
	/**
	 * returns the array constructing a button in twig
	 * 
	 * standard buttons are: ok, cancel, command (set the default of a command)
	 * 
	 * 
	 * @param mixed $options array or name of standard action
	 */
	public function button($options)
	{
		$btn = array(
			'default' => false,				// use the default of one the standard button. default is the name of the button	
			'label' => Yii::t('button','btn-no-caption'),
			'url' => false,			
			'position' => 'pull-right',
			'type' => 'command',	
			'style' => 'btn btn-info',
			'width' => null,	
			'visible' => true,
			'ask' => false,	
      'id' => false, 
		);
		if (is_string($options) || (is_array($options) && isset($options['default']))) {
			$act = (is_array($options) && isset($options['default'])) ? $options['default'] : $options;
			switch ($act) {
				case 'submit' : 
					$btn = array_merge($btn, array(
							'label'  => false, //'btn-submit',
							'position' => 'pull-right',
							'type' => 'submit',
							'style' => 'btn btn-primary',
					));		
					break;
				case 'cancel' :	
					$btn = array_merge($btn, array(
							'label'  => Yii::t('button','btn-cancel'),
							'position' => 'pull-left',
							'type' => 'cancel',
							'style' => 'btn btn-default',
					));		
					break;					
				case 'delete' :	
					$btn = array_merge($btn, array(
							'label'  => Yii::t('button', 'btn-delete'),
							'position' => 'left',
							'type' => 'delete',
							'action' => 'delete',
							'style' => 'btn btn-warning',
					));		
					break;					
				
				default :
					$btn['visible'] = false;
					Yii::log('Unknown standard action '.$act.' for a button', CLogger::LEVEL_WARNING, 'toxus.BaseController.button');					
					break;
			}			
		}
		
		if (is_array($options)) {
			foreach ($options as $key => $option) {
				if (isset($btn[$key])) {
					$btn[$key] = $option;
 				} else {
					Yii::log('The option '.$key.' is undefined for a button', CLogger::LEVEL_WARNING, 'toxus.BaseController.button');
				}
			}
		}
		return $btn;
	}
	
	/**
	 * List the article to be shown in the footer
	 * 
	 * @return array of article => key
	 */
	public function getFooterArticles() {
		$ret = new ArticleModel();
		return $ret->findFooterArticles();
	}
	
	
	/**
	 * to place a button on the toolbar
	 * @param boolean $isStart true if it before the other buttons
	 * @return string
	 */
	public function getToolButtons($isStart)
	{
		return '';
	}	
}