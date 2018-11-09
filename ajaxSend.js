

     
  function callScript(val){
      
        
            jQuery(function($) {
                      
		              var data = {
			         'action': 'luf_function',
			         'role':     val
                    };

		          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    $.post( ajax_object.ajax_url, data, function(response) {
                    console.log('role: ' + data.role)
                    console.log('role: ' + data.action)
			         console.log('Got this from the server: ' + response);
                    //$('#divAppend').html(response);
		          }   );
	           });

 }   