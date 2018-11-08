

     
  function callScript(val){
      
        
            jQuery(document).ready(function($) {
                      
		              var data = {
			         'action': 'luf_ajax_function',
			         'role':     val
                    };

		          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post( ajax_object.ajax_url, data, function(response) {
			         console.log('Got this from the server: ' + response);
		          });
	           });

 }   