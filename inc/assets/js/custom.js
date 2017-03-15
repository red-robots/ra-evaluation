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
	if(bella.admin){
	    jQuery('.js-datepicker').datepicker();
    }


    if(!bella.admin){
	    $('.ra-node').click(function(){
            var $this = $(this);
            var val;
            //toggle visually
            if($this.hasClass('active')){
                $this.removeClass('active');
                val = 0;
            } else {
                $this.addClass('active');
                val = 1;
            }
            var regex = new RegExp('(right-\\d{1,2})|(left-\\d{1,2})|(middle-\\d{1,2})');
            var classes = this.className;
            var matches = classes.match(regex);
            if(matches) {
                matches.forEach(function (match, i, array) {
                    $('input[name="' + match + '"]').val(val);
                });
            }
        });
    }

	/*
	*
	*	Wow Animation
	*
	------------------------------------*/
	//new WOW().init();

});// END #####################################    END