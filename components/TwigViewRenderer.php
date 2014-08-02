<?php

// Yii::import('application.vendor.toxus.extensions.ETwigViewRenderer');
Yii::import('toxus.extensions.ToxusTwigRenderer');


function YiiTranslate($group='app', $message='', $params=array())
{
	return Yii::t($group,$message,$params);
}

class TwigViewRenderer extends ToxusTwigRenderer
{
	public $twigPathAlias = 'toxus.extensions.Twig';
}