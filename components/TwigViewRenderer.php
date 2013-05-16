<?php

// Yii::import('application.vendors.toxus.extensions.ETwigViewRenderer');
Yii::import('toxus.extensions.ETwigViewRenderer');
class TwigViewRenderer extends ETwigViewRenderer
{
	public $twigPathAlias = 'application.vendors.toxus.extensions.Twig';
}