<?php
/**
 * the menu for the user
 */
if (Yii::app()->user->isGuest) {
	return array(
		'sign-up' => array(
			'label' => Yii::t('base','sign up',1),
			'url' => $this->createUrl('/register'),
			'icon' => 'icon-user',	
		),
		'sign-in' => array (
			'label' => Yii::t('base','sign in', 1),
			'url' => $this->createUrl('/login'),						
		),	
	);
} else {
	$menu = array(
		'user' => array(
			'label' => ucfirst(Yii::app()->user->profile->username),
			'url' => $this->createUrl('profile/index'),	
		),	
		'sign-out' => array (
			'label' => Yii::t('base','sign out', 1),
			'url' => $this->createUrl('profile/logout'),						
		),	
	);
}
return $menu;
