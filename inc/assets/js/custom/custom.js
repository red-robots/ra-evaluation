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
    *   Tabs
    *
    ------------------------------------*/
    $('ul.tabs').each(function(){
      // For each set of tabs, we want to keep track of
      // which tab is active and its associated content
      var $active, $content, $links = $(this).find('a');

      // If the location.hash matches one of the links, use that as the active tab.
      // If no match is found, use the first link as the initial active tab.
      $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
      $active.addClass('active');

      $content = $($active[0].hash);

      // Hide the remaining content
      $links.not($active).each(function () {
        $(this.hash).hide();
      });

      // Bind the click event handler
      $(this).on('click', 'a', function(e){
        // Make the old tab inactive.
        $active.removeClass('active');
        $content.hide();

        // Update the variables with the new link and content
        $active = $(this);
        $content = $(this.hash);

        // Make the tab active.
        $active.addClass('active');
        $content.show();

        // Prevent the anchor's default click action
        e.preventDefault();
      });
    });

	/*
	*
	*	Wow Animation
	*
	------------------------------------*/
	//new WOW().init();

});// END #####################################    END