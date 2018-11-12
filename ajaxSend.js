

     
  function callScript(val){


            jQuery(function($) {
                      
		              var data = {
			         'action': 'luf_function',
			         'role':     val
                    };


                console.log('action: ' + data.action);
                console.log('role: ' + data.role);
                console.log('jquery: ' + $ );

		          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                $.post( ajax_object.ajaxurl, data, function(response) {

                      //  console.log('ajaxobj: ' + ajax_object.ajaxurl)
                        //			         console.log('Got this from the server: ' + response);
                    $('#divAppend').html(response);
		          }   );
	           });


}