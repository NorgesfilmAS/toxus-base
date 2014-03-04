<?php

class SetupFormModel extends CFormModel
{
	/**
	 * change to use an other extended class
	 * @var string
	 */
	public $setupClass = 'SetupFormModel';
	
	public $isNewRecord = false;	
	public function isEditable($key)
	{
		return  true;
	}	
	/**
	 * generate the form based on the loaded configuration
	 * 
	 * all form variables will have the format of SectionName[parameter]
	 */
	public function generateForm()
	{
		$group = false;
		$mark = '';
		
		$form = array(
			'title' => Yii::t('config','System setup'),
			'model' => $this->setupClass,	
		);		
		$elements = array();
		foreach (Yii::app()->config->sections() as $sectionName => $section) {
			if (isset($section['group']) && $group != $section['group']) {
				$mark = '<a name="'.md5($section['group']).'"></a>';
				$group = $section['group'];				
			} else {
				$mark = '';
			}
			$elements[$sectionName.'Header'] = array(
				'value' => $mark.'<h4>'.  CHtml::encode($section['label']).'</h4>',
				'hideLabel' => true,
				'type' => 'raw'	
			);
			foreach ($section['items'] as $varName => $properties) {
				$type = isset($properties['type']) ? $properties['type'] : 'text';
				$a = array(
					'label' => isset($properties['label']) ? $properties['label'] : $varName,	
				);
				if ($type == 'text') {
					$a['type'] = 'text';
				} elseif ($type == 'boolean') {
					$a['type'] = 'checkbox';
				}
				$a['tooltip'] = isset($properties['info']) ?$properties['info'] : false;
				$elements[$sectionName.'-'.$varName] = $a;
			}
		}
		$form['elements'] = $elements;
		$form['buttons'] = 'default';
		return Yii::app()->controller->parseForm($form);
	}	
	
	public function __get($name)
	{
		$a = explode('-', $name);
		$c = Yii::app()->config;
		$p = $c->$a[0];
		if (isset($p)) {
			return $p[$a[1]];
		}
	}
	/**
	 * saves the configuration as system setup
	 */
	public function save()
	{
		return Yii::app()->config->save('setup');
	}
	
	public function setAttributes($values,$safeOnly=true)
	{
		if(!is_array($values)) {
			return;
		}	

		foreach($values as $name => $value)	{
			$a = explode('-', $name);
			$section = $a[0];
			$key = $a[1];
			if (isset(Yii::app()->config->$section)) {
				Yii::app()->config->{$section}[$key] = $value;
			} else if($safeOnly) {
				$this->onUnsafeAttribute($name,$value);
			}	
		}
	}
	
}