<?php
/**
 * generate the item menu
 */
$menu = array();
$group = false;

foreach (Yii::app()->config->sections() as $sectionName => $section) {
	if (isset($section['group']) && $group != $section['group']) {
		$menu[$section['group']] = array(
			'label' => $this->te($section['group']),
			'url' => '#'.md5($section['group'])
		);							
	} else {
		$mark = '';
	}
}
return $menu;