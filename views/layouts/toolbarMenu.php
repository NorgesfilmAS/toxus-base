<?php
/**
 * the menu for the user
 */
$menu = array();
if (Yii::app()->user->profile->rights_id >= UserProfile::MODERATOR) {
	$menu = array(
		'article-new' => array (
			'label' => $this->t('Add', 1),
			'icon' => 'icon-plus',
			'tooltip' => 'Add a new Article '.(isset($this->model) ? 'Model set' : 'Not set'),	
			'url' => $this->createUrl('article/create'),						
		)
	);		
	$menu['message-list'] = array(
			'label' => $this->t('Message', 1),
			'icon' => 'icon-envelope',
			'tooltip' => 'List message',	
			'url' => $this->createUrl('message/list'),												
	);
	$menu['profile-list'] = array(
			'label' => $this->t('Profiles', 1),
			'icon' => 'icon-group',
			'tooltip' => 'List Profiles',	
			'url' => $this->createUrl('profile/list'),												
	);
	
}
return $menu;
