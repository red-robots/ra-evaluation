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
        jQuery('.js-datepicker').datepicker();

        $('body').on('click',function(){
            $('.ra-selector-box').detach();
            $('.ra-node.box-open').removeClass("box-open");
        });
	    $('.ra-node').click(function(e){
	        e.preventDefault();
	        e.stopPropagation();
            var $node_this = $(this);
            var node_this = this;
            var node_classes = this.className;
            var val = 0;
            $('.ra-selector-box').detach();
            $('.ra-node.box-open').not($node_this).removeClass("box-open");
            if(!$node_this.hasClass("box-open")){
                $selector_box = $('<div class="ra-selector-box"></div>');
                $selector_t = $('<div class="box active-1">tender</div>');
                $selector_s = $('<div class="box active-2">swollen</div>');
                $selector_ts = $('<div class="box active-3">tender and swollen</div>');
                $selector_n = $('<div class="box inactive">deselect</div>');
                $selector_box.append($selector_t,$selector_s,$selector_ts,$selector_n);
                $node_this.append($selector_box);
                $node_this.addClass("box-open");
                $selector_box.on('click',process_selector);
            } else {
                $node_this.removeClass("box-open");
            }
            function process_selector(e){
                e.preventDefault();
                e.stopPropagation();
                $selector_box.detach();
                var selector_classes = e.target.className;
                var selector_regex_active = new RegExp('active-\\d');
                node_this.className = node_classes.replace(selector_regex_active,"").trim();
                var selector_matches_active = selector_classes.match(selector_regex_active);
                if(selector_matches_active){
                    var selector_splits = selector_matches_active[0].split("-");
                    val = new Number(selector_splits[1]);
                    $node_this.addClass(selector_matches_active[0]);
                } else {
                    var selector_regex_inactive = new RegExp('inactive');
                    var selector_matches_inactive = selector_classes.match(selector_regex_inactive);
                    if (selector_matches_inactive) {
                        val = 0;
                    }
                }
                var node_regex = new RegExp('(right-\\d{1,2})|(left-\\d{1,2})|(middle-\\d{1,2})');
                var node_matches = node_classes.match(node_regex);
                if (node_matches.length > 0) {
                    node_matches.forEach(function (match, i, array) {
                        $('input[name="' + match + '"]').val(val);
                    });
                }
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