<?php

Yii::import('toxus.models._base.BaseCoupon');

class CouponModel extends BaseCoupon
{
	public $startDate = null;
	public $endDate = null;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function relations()
	{
		return array(
				'payments' => array(self::HAS_MANY, 'Payment', 'coupon_id')
		);
	}
	public function afterFind() {
		$this->startDate = Util::dateDisplay($this->start_date);
		return parent::afterFind();
	}
	
	/**
	 * 
	 * @return integer the count this coupon is used
	 */
	public function getUsedCount()
	{
		$cnt = 0;
		foreach ($this->payments as $payment) {
			if ($payment->status_id == Payment::PAYMENT_SUCCESS) { 
				$cnt++;
			}
		}
		return $cnt;
	}
	
	public function getIsActive()
	{
		return ($this->is_active == 1); /* &&
					($this->start_date == 0 || $this->start_date > date('Y-m-d')) &&
					($this->end_date == 0 || $this->end_date <= date('Y-m-d')) &&
					($this->max_use_count == 0 || $this->usedCount < $this->max_use_count);
		 * 
		 */
	}
	
}