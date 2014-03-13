<?php

return array(
		'title' => Yii::t('config', 'Admin module'),
		//'model' => 'Login',
		'model' => 'SetupFormModel',
		'elements' => array(
				'password' => array(
						'type' => 'password',
						'label' => $this->te('password'),
				),
		),
		'buttons' => array(
				'edit' => array(
						'submit' =>$this->button( array(
					'default' => 'submit',
					'label' => Yii::t('config', 'Login')
				))
			)				
		),
);
