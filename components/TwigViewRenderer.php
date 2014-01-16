<?php

// Yii::import('application.vendor.toxus.extensions.ETwigViewRenderer');
Yii::import('toxus.extensions.ToxusTwigRenderer');
class TwigViewRenderer extends ToxusTwigRenderer
{
	public $twigPathAlias = 'toxus.extensions.Twig';
}