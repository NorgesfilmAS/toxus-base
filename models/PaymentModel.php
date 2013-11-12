<?php
/**
 * the payment record.
 * 
 * The payment / invoice basis. 
 * 
 * To create a new payment set: profile_id (current user if 0), product_id, amount (ex vat) and the description
 * 
 * 
 * IF ANY VALUE IS CHANGE IN Payment or Coupon CALL: recalculate() and save()
 * 
 * CALCULATIONS
 * all amount are stored with a . for decimal point
 * 
 * amount												: fixed: the amount ex vat given
 * amount_ex_vat								: calc:	 amount of calculated
 * discount_amount_ex_vat				: calc:  the amount of discount on the amount_ex_vat
 * discount_amount							: calc:  the amount of discount on the total_amount
 * total_amount_ex_vat 				  : calc:  the amount_ex_vat - discount_amount_ex_vat - discount_amount
 * 
 * vat_percentage								: fix: the given percentage
 * vat_amount										: calc: the total amount of vat calculated
 * total_amount									: calc: the price paid: total_amount_ex_vat + vat_amount
 * 
 * 
 * examples:
 *				vat_percentage = 20		(fix for all examples)
 *				amount = 100					(fix for all examples)
 * 
 *				amount_ex_vat = 100
 *			  discount_amount_ex_vat = 10 (discount_amount is not read any more)
 *				total_amount_ex_vat = 90
 *				vat_amount = 18	
 *				total_amount = 108
 * 
 * example 2:
 *			  discount_amount = 10 (discount_amount_ex_vat = null)
 *				amount_ex_vat = 91.67 (total_amount / (100 + vat_percentage / 100))
 *				vat_amount = 18.33	(total_amount - amount_ex_vat)
 *				total_amount = 110  (total_amount_ex * (100 + vat_amount) / 100) - discount_amount

 */

Yii::import('toxus.models._base.BasePayment');

class PaymentModel extends BasePayment
{
	const PAYMENT_NONE = 0;						// not set yet
	const PAYMENT_STARTING = 1;				// before contact provider
	const PAYMENT_TRANSACTION = 2;		// waiting for provider to responde
	const PAYMENT_SUCCESS = 3;				// provider said yes
	const PAYMENT_CANCEL = 4;					// provider said no
	const PAYMENT_PENDING = 5;				// not used (notification happend)
	const PAYMENT_ERROR = 6;					// error happend: see status_text
	
	/**
	 * the name of the class used to store the coupons in.
	 * 
	 * @var string
	 */
	protected $couponModel = 'CouponModel';
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function rules() {
		$r = parent::rules();
		unset($r[0]);		// slug, required   should not be in the rules section !
		return $r;
	}
	
	public function relations() {
		return array(
			'coupon' => array(self::BELONGS_TO, $this->couponModel, 'coupon_id')	
		);
	}
	
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->slug = Util::generateRandomString(40);
			$this->creation_date = new CDbExpression('NOW()');
			if ($this->profile_id == 0) {
				$this->profile_id = Yii::app()->user->profile_id;
			}
			if ($this->vat_percentage == null) {
				$this->vat_percentage = 21;	// should be done in the config
			}	
		}
		return parent::beforeSave();
	}
	/**
	 * retrieve the code for the coupon
	 * 
	 * @return string
	 */
	public function getCouponCode()
	{
		return $this->discount_code;
	}
	/**
	 * changes the discount to the new one and recalculates the record
	 * 
	 * it does NOT save the changes
	 */
	public function setCouponCode($value)
	{
		$this->coupon_id = 0;
		$this->discount_code = null;
		$m = $this->couponModel;
		$coupons = $m::model()->findAll();
		foreach ($coupons as $coupon) {
			if ($coupon->slug == $value && $coupon->isActive) {
				$this->discount_code = $value;
				$this->coupon_id = $coupon->id;
				break;	
			} 
		}		
		$this->recalculate();
	}
	/**
	 * 
	 * @return boolean true if there is a functional coupon
	 */
	public function getHasCouponCode()
	{
		return $this->coupon_id > 0;
	}
	/**
	 * 
	 * @return boolean true if coupon's discount is ex vat
	 */
	public function getIsDiscountExVat()
	{
		return ($this->coupon->amount_ex_vat > 0 || $this->coupon->percentage_ex_vat);
	}
	
	/**
	 * recalculates the payment including the discount
	 * 
	 */
	public function recalculate()
	{
		// if the coupon was changed during the operation, we would not see it
		$this->getRelated('coupon', true);
		// clear all calculated values
		$this->discount_amount = 0;		
		$this->discount_amount_ex_vat = null;		
		$this->amount_ex_vat = 0;
		$this->total_amount_ex_vat = 0;
		$this->vat_amount = 0;
		$this->total_amount = 0;		
		
		if ($this->hasCouponCode ) { 
			if ($this->isDiscountExVat) {
				// calculate the discount from the price ex vat
				if ($this->coupon->amount_ex_vat > 0) {
					$this->amount_ex_vat = $this->amount;
					$this->discount_amount_ex_vat = $this->coupon->amount_ex_vat;
				} elseif ($this->coupon->percentage_ex_vat > 0) {
					$this->amount_ex_vat = $this->amount;
					$this->discount_amount_ex_vat = round($this->amount_ex_vat * $this->coupon->percentage_ex_vat / 100, 2);				
				}		
				$this->total_amount_ex_vat = $this->amount_ex_vat - $this->discount_amount_ex_vat;
				if ($this->vat_percentage > 0) {
					$this->vat_amount = round($this->total_amount_ex_vat * $this->vat_percentage / 100, 2); 					
				} 
				$this->total_amount = $this->total_amount_ex_vat + $this->vat_amount;
				
			} else {
				$total = $this->amount;
				// we need the total amount inc vat ==>> $total
				if ($this->vat_percentage > 0) {
					$total +=  round($this->amount * $this->vat_percentage / 100, 2); 					
				} 				
				// discount will go from the total amount
				if ($this->coupon->amount > 0) {
					$this->discount_amount = $this->coupon->amount;
				} elseif ($this->coupon->percentage > 0)  {
					$this->discount_amount = round($total * $this->coupon->percentage / 100, 2);
				}
				$this->total_amount = $total - $this->discount_amount;
				$this->total_amount_ex_vat = round($this->total_amount / ((100 + $this->vat_percentage) / 100),2);
				$this->vat_amount = $this->total_amount - $this->total_amount_ex_vat;
			}
		} else {
			$this->amount_ex_vat = $this->amount;
			if ($this->vat_percentage > 0) {
				$this->vat_amount = round($this->amount_ex_vat * ($this->vat_percentage / 100),2);
			}	
			$this->total_amount_ex_vat = $this->amount_ex_vat;
			$this->total_amount = $this->total_amount_ex_vat + $this->vat_amount;
		}		
	}
}