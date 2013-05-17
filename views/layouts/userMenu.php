<?php
/**
 * the menu for the user
 */
if (Yii::app()->user->isGuest) {
	return array(
		'sign-up' => array(
			'label' => $this->t('sign up',1),
			'url' => $this->createUrl('/register'),
			'icon' => 'icon-user',	
		),
		'sign-in' => array (
			'label' => $this->t('sign in', 1),
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
			'label' => $this->t('sign out', 1),
			'url' => $this->createUrl('profile/logout'),						
		),	
	);
}
return $menu;
