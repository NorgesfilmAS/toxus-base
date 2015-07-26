/**
 * core function to extend 
 * 
 */

/**
 * convert a number into a currency string
 * call:
 *    nr = '12.3';
 *			alert(nr.formatMoney(2,',','.')); // whill alert 12,30
 */
Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}; 

String.prototype.replaceAll = function (find, replace) {
    var str = this;
    return str.replace(new RegExp(find, 'g'), replace);
};

// https://github.com/twitter/bootstrap/pull/5514
$().ready(function() {
	
	$('#id-modal').on('hidden',function(){
    $(this).removeData('modal');
    $('#id-modal .modal_label').text('default_label');
    $('#id-modal .modal_body').html('default_body');
	});
});	

/*
	 * Vibrating Alerts
	 *
	 * For more information, visit the project's homepage (http://prashant.es/labs/vibrating-notices/)
	 *
	 * Copyright (c) 2010 Prashant Sugand (prashant.es)
	 * Licensed under the MIT license. http://www.opensource.org/licenses/mit-license.php
	 */

	jQuery.fn.vibrate = function(axis, distance, repetition, duration) {

			var i = 0;
			var o = distance / distance;

			switch(axis) {
				case 'x':
					while(i < repetition) {
						$(this).animate({marginLeft:'-'+distance+'px'}, duration);
						$(this).animate({marginLeft:distance}, duration);
						i++;
						if(i == repetition) {
							$(this).animate({marginLeft:o}, duration);
						}
					}
					break;

				case 'y':
					while(i < repetition) {
						$(this).animate({marginTop:'-'+distance+'px'}, duration);
						$(this).animate({marginTop:distance}, duration);
						i++;
					}
					break;
			}
	}		
		
	$().ready( function() {		
		$('.shake').vibrate('x', 5, 3, 85);
	});
