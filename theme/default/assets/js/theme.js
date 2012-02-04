jQuery(function() {

	/**
	 * Open menu on click
	 */
	jQuery(".menu-wrapper a").on("click", function(evt){
		var sub = jQuery(this).next("ul.sub-menu");

		// If there is a sub menu for the item
		if (sub.length)
		{
			// Prevent default action
			evt.preventDefault();

			sub.css("display", "block");
		}
	});

	/**
	 * Close menu on mouseleave
	 */
	jQuery(".menu-wrapper li").on("mouseleave", function(){
		var sub = jQuery(this).children("ul.sub-menu");

		sub.css("display", "none");
	});

});