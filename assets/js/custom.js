/**
 *	Custom jQuery Scripts
 *	
 *	Developed by: Austin Crane	
 *	Designed by: Austin Crane
 */

jQuery(document).ready(function ($) {
	
	/*
	*
	*	jQuery for datepicker
	*
	------------------------------------*/
	jQuery('.js-datepicker').datepicker(); 


	$( function() {
	    $( ".inactive" ).on( "click", function() {
	      $(this).toggleClass( "active", 1000 );
	    });
	} );

	/*
	*
	*	Wow Animation
	*
	------------------------------------*/
	new WOW().init();

});// END #####################################    END