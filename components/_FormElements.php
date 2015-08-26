<?php

Yii::import('application.controllers.ArticleController');
class FormElements extends CComponent
{
	private $_controller;
	

	
	public function getController()
	{
		if ($this->_controller == null) {
			$this->_controller = Yii::app()->controller;
		}
		return $this->_controller;
	}
	
	public function htmlEditor($model, $attribute, $template='full', $options=array())
	{
		$this->controller->widget('ext.tinymce.TinyMce', array(
				'model' => $model,
				'attribute' => $attribute,
				// Optional config
				'compressorRoute' =>  false, //'tinyMce/compressor',
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
							'convert_urls' => false,

							'theme_advanced_buttons1' =>
								'undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent, indent,|,|,sub,sup,|,bullist,numlist,|,link,code,|,image',
								'theme_advanced_buttons2' => 'formatselect,styleselect,|,cut,copy,paste,pastetext,pasteword,|,search,replace, tablecontrols,|,removeformat,visualaid,|,tablecontrols',
								'theme_advanced_buttons3' => '',
								'theme_advanced_buttons4' => '',
//								'theme_advanced_buttons4' => '',
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
		$this->controller->widget(
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
		
		return $this->controller->widget(
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

	public function chosen($model, $attribute, $options=array(), $htmlOptions=array())
	{
		if (!isset($options['items'])) $options['items'] = array();
		if (!isset($options['multi'])) $options['multi'] = true;
		$this->controller->widget(
			'ext.chosen.Chosen',
			array(
					'model' => $model,
					'attribute' => $attribute,
					'data' => $options['items'],
					'multiple' => $options['multi'],
					'htmlOptions' => array('class' => 'input-xlarge'),
					'placeholderMultiple' => isset($htmlOptions['placeholder']) ? $htmlOptions['placeholder'] : '',
			)			
		);
	}
	
	public function page($id, $template = '//layouts/_stringTemplate', $params = array())	
	{
		$model = Article::model()->findByPk($id);
		if ($model == null) return '';
		
		return $this->controller->renderPartial($template, array('model' => $model, 'params' => $params));
	}
	
}