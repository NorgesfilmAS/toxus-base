<?php

Yii::import('toxus.models._base.BasePaymentCoupon');

class PaymentCouponModel extends BasePaymentCoupon
{
	const FORMAT_POSTFIX = '_FMT';

	
	public $startDate = null;
	public $endDate = null;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function relations()
	{
		return array(
				'payments' => array(self::HAS_MANY, 'PaymentModel', 'coupon_id')
		);
	}

	
	
	public function __get($name)
	{
		if (substr($name, - strlen(self::FORMAT_POSTFIX)) == self::FORMAT_POSTFIX) {
			return Util::dateTimeToString(parent::__get(substr($name, 0, - strlen(self::FORMAT_POSTFIX)) ));
		} else {
			return parent::__get($name);
		}	
	}
	
	/**
	 * 
	 * @return integer the count this coupon is used
	 */
	public function getUsedCount()
	{
		$cnt = 0;
		$p = $this->getRelated('payments', true);
		foreach ($p as $payment) {
			if ($payment->status_id == Payment::PAYMENT_SUCCESS) { 
				$cnt++;
			}
		}
		return $cnt;
	}
	
	public function getIsActive()
	{
		$now = new DateTime();
		$firstDate = new DateTime('2000-01-01');
		return ($this->is_active == 1)  &&
					($this->start_date < $firstDate || $this->start_date < $now  ) &&
					($this->end_date < $firstDate || $this->end_date >= $now) &&
					($this->max_use_count == 0 || $this->usedCount < $this->max_use_count);
	}	
}