

(function($) {
 // function callScript(val){
 $('#cmbRole').on('change', function(){

                      
		              var data = {
			         'action': 'luf_function',
			         'role':     $('#cmbRole').val()
                    };


                console.log('action: ' + data.action);
                console.log('role: ' + data.role);
                console.log('jquery: ' + $ );


                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'post',
                    data: data,
                    success: function( response ) {
                        $('#divAppend').html(response);
                    }
                })



		          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

	           });

})(jQuery);