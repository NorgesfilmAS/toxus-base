<?php

Yii::import('toxus.models._base.BasePaymentStatus');

class PaymentStatusModel extends BasePaymentStatus
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}