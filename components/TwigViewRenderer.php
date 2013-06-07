<?php

// Yii::import('application.vendors.toxus.extensions.ETwigViewRenderer');
Yii::import('toxus.extensions.ToxusTwigRenderer');
class TwigViewRenderer extends ToxusTwigRenderer
{
	public $twigPathAlias = 'application.vendors.toxus.extensions.Twig';
}