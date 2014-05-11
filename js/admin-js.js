jQuery(document).ready(function($){
	$('#od_mobile_search_label').live('click', function(){
		if( $('#od_mobile_search_label:checked') ){
			$(this).val('true');
		}
		else{
			$(this).val('false');
		}
	});
	
	$('#od_mobile_float_label').live('click', function(){
		if( $('#od_mobile_float_label:checked') ){
			$(this).val('true');
		}
		else{
			$(this).val('false');
		}
	});

	$('a#od-menu-options').live('click', function(){
		if( $(this).hasClass('od-hidden') ){
			$('#phone-custom-link').slideDown('slow');
			$(this).addClass('od-shown');
			$(this).removeClass('od-hidden');
			$(this).text('Hide options');
		}
		else{
			if( $(this).hasClass('od-shown') ){
				$('#phone-custom-link').slideUp('slow');
				$(this).addClass('od-hidden');
				$(this).removeClass('od-shown');
				$(this).text('Show options');
			}
		}
		return false;
	});

	$('.wp-admin #mobile-header').css( 'background', $('#od_mobile_bg_color').val() );
	
	if( $('#od_mobile_phone_label').val() != '' ){ 
		$('.wp-admin #mobile-header a.right').text( $('#od_mobile_phone_label').val() );
	}
	
});