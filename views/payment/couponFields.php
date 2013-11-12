<?php
return array(
		'model' => 'Payment',
		'elements' => array(
			'discount' => array(	
				'elements' => array(	
					'coupon' => array(
						'type' => 'text',
						'width' => 'col-sm-7',	
					),
					'submit' => array(
						'type' => 'button',
						'width' => 'pull-right',
						'style' => 'btn-primary',	
						'caption' => $this->te('redeem'),	
						'script' => '$("#id-form").submit();	'
					)	
				),		
			),		
			'next' => array(					
				'type' => 'button',
				'width' => 'pull-right',
				'caption' => 'next (payment)', // <span class="glyphicon glyphicon-chevron-right"></span>',
				'hideLabel' => 'true',	
				'script' => 'location.href="'.$this->createUrl('payment/provider', array('id' => $this->model->slug)).'"',	
				'style' => 'btn-primary',		
			)	
		),
);