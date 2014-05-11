jQuery(document).ready(function($){

	$('ul.mm-submenu').removeAttr('style');
	

	/** This is the fix to make mobile menu remain on top or fixed when entering data on forms */

	if( jQuery.browser.mobile ){

		$('.gform_wrapper input').focus(function(){

			$('#mobile-header').css('position', 'relative');

		});

		

		$('.gform_wrapper input').focusout(function(){

			$('#mobile-header').css('position', 'fixed');

		});

		

		$('.gform_wrapper textarea').focus(function(){

			$('#mobile-header').css('position', 'relative');

		});

		

		$('.gform_wrapper textarea').focusout(function(){

			$('#mobile-header').css('position', 'fixed');

		});

		

		$('.gform_wrapper select').focus(function(){

			$('#mobile-header').css('position', 'relative');

		});

		

		$('.gform_wrapper select').focusout(function(){

			$('#mobile-header').css('position', 'fixed');

		});

		

	}

	

});