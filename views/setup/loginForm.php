<?php

return array(
		'title' => Yii::t('base', 'Admin module'),
		//'model' => 'Login',
		'model' => 'SetupFormModel',
		'elements' => array(
				'password' => array(
					'type' => 'password',
					'label' => Yii::t('base', 'Password'),
				),
		),
		'buttons' => array(
				'edit' => array(
					'submit' =>$this->button( array(
					'default' => 'submit',
					'label' => Yii::t('base', 'Login')
				))
			)				
		),
);
