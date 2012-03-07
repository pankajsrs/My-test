jQuery.noConflict();

var BASE_URL = jQuery('#BASE_URL').val();
var PUBLIC_URL = jQuery('#PUBLIC_URL').val();


function get_search_results()
{
	jQuery.ajax({
	  url:  BASE_URL+"index/get-search-results/",
	  success: function(rsp){
		 alert(rsp);
	  }
	});
}

jQuery(document).ready(function () {
	setTimeout( function() {
		jQuery('#flashMessage').hide('fadeout');
	}, 4000 );
});