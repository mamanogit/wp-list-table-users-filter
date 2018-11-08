$('#cmbRole').on('change', function(){

            console.log("Role selected: ". $('#cmbRole').val() );
            
            jQuery(document).ready(function($) {

		              var data = {
			         'action': 'my_action',
			         'role': val
                    };

		          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(`<?= LUF_PLUGIN_URL ?>/controllerUsers.php`, data, function(response) {
			         console.log('Got this from the server: ' + response);
		          });
	           });

)}