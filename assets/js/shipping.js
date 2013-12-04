var edd_global_vars;
jQuery(document).ready(function($){
	$('body').on('change', 'select[name=shipping_country],select[name=billing_country]',function(){

		var billing = true;

		if( $('select[name=billing_country]').length && ! $('#shoestrap_edd_simple_shipping_show').is(':checked') ) {
			var val = $('select[name=billing_country]').val();
		} else {
			var val = $('select[name=shipping_country]').val();
			billing = false;
		}

		if( billing && edd_global_vars.taxes_enabled == 1 )
			return; // EDD core will recalculate on billing address change if taxes are enabled

		if( val =='US') {
			$('#shipping_state_other').hide();$('#shipping_state_us').show();$('#shipping_state_ca').hide();
		} else if(  val =='CA'){
			$('#shipping_state_other').hide();$('#shipping_state_us').hide();$('#shipping_state_ca').show();
		} else {
			$('#shipping_state_other').show();$('#shipping_state_us').hide();$('#shipping_state_ca').hide();
		}
		var postData = {
			action: 'edd_get_shipping_rate',
			country:  val
		};
		$.ajax({
			type: "POST",
			data: postData,
			dataType: "json",
			url: edd_global_vars.ajaxurl,
			success: function (response) {
				if( response ) {
					$('.edd_cart_amount').text( response.total );
					$('#edd_cart_fee_simple_shipping .edd_cart_fee_amount').text( response.shipping_amount );
				} else {
					console.log( response );
				}
			}
		}).fail(function (data) {
			console.log(data);
		});
	});

	$('body').on('edd_taxes_recalculated', function( event, data ) {

		if( $('#shoestrap_edd_simple_shipping_show').is(':checked') )
			return;

		var postData = {
			action: 'edd_get_shipping_rate',
			country: data.postdata.country
		};
		$.ajax({
			type: "POST",
			data: postData,
			dataType: "json",
			url: edd_global_vars.ajaxurl,
			success: function (response) {
				if( response ) {
					$('.edd_cart_amount').text( response.total );
					$('#edd_cart_fee_simple_shipping .edd_cart_fee_amount').text( response.shipping_amount );
				} else {
					console.log( response );
				}
			}
		}).fail(function (data) {
			console.log(data);
		});

	});

	$('select#edd-gateway, input.edd-gateway').change( function (e) {
		var postData = {
			action: 'edd_get_shipping_rate',
			country: 'US' // default
		};
		$.ajax({
			type: "POST",
			data: postData,
			dataType: "json",
			url: edd_global_vars.ajaxurl,
			success: function (response) {
				if( response ) {
					$('.edd_cart_amount').text( response.total );
					$('#edd_cart_fee_simple_shipping .edd_cart_fee_amount').text( response.shipping_amount );
				} else {
					console.log( response );
				}
			}
		}).fail(function (data) {
			console.log(data);
		});
	});
	$('#shoestrap_edd_simple_shipping_show').change(function(){
		$('#shoestrap_edd_simple_shipping_fields_wrap').toggle();
	});
});