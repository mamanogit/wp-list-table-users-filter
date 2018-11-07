<?php
/*
Plugin Name: WP List Users Filtered
Description: List users filtered
Author: skarioasto
Author URI: https://github.com/Sonecaa
Version: 0.1.0
License: MIT
*/

define( 'LUF_PLUGIN_DIR', __DIR__ );
define( 'LUF_PLUGIN_URL', plugins_url(null, __FILE__) );


add_action( 'admin_menu', 'inject_admin' );

function inject_admin() {
    add_menu_page( 'WP Users Filtered', 'WP Users Filtered', 'manage_options', 'wp-listusersfiltered/listusersfiltered.php', 'list_table_users_filtered', 'dashicons-id-alt', 1  );
}




function list_table_users_filtered( ) {

    ?>
    


    <div class="alert alert-primary" role="alert">
        WP Users Filtered
    </div>
    
    <div>
       <?php    
    global $wp_roles;
    $all_roles = $wp_roles->roles;
    //echo "<pre>" . var_dump($all_roles); "</pre>";
        ?>
    </div>

    <div class="container">
        <select class="custom-select luf-cmbFilter" id="cmbRole" onchange="filterByRoleName($(this).val())">
             <option selected disabled>Select role</option>
            <?php foreach ($all_roles as $roleeach): ?>         
            <option value="<?= $roleeach['name'] ?>"><?=  $roleeach['name'] ?></option>
            <?php
                endforeach; ?>
        </select
            

        <hr />

    
    </div>
        
        
   
        
        
    
    <?php
}

add_shortcode( 'listusersfiltered', 'list_table_users_filtered' );



//INCLUDES
require_once LUF_PLUGIN_DIR . '/_inc/include.php';






add_action( 'wp_ajax_my_action', 'my_action' );

function my_action() {
	global $wpdb; // this is how you get access to the database

	$whatever = intval( $_POST['whatever'] );

	$whatever += 10;

        echo $whatever;

	wp_die(); // this is required to terminate immediately and return a proper response
}


add_action( 'admin_footer', 'wp_ajax_call_filter' );

function wp_ajax_call_filter() { ?>
         <script type="text/javascript">
        
        function filterByRoleName(val){
            console.log("Role selected: ". val);
            
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
        }
        
</script> <?php
}

