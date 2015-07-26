<?php

/**
 * special version of ETwigRenderer
 * will change the load temporary to a string loader
 */
Yii::import('toxus.extensions.ETwigViewRenderer');
class ToxusTwigRenderer extends ETwigViewRenderer
{
	private $_orgLoader;
	private $_stringLoader;
	
	public function init()
	{
		$result = parent::init();
		$this->_orgLoader = $this->twig->getLoader();
		$this->_stringLoader = new Twig_Loader_String();
		return $result;
	}
	
	public function renderString($context, $sourceString, $data)
	{
		// current controller properties will be accessible as {{ this.property }}
		$data['this'] = $context;

//		$sourceFile = substr($sourceFile, $this->_basePathLength);
		$this->twig->setLoader($this->_stringLoader);
		$template = $this->twig->render($sourceString, $data);

		return $template;
	}
}