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

require_once LUF_PLUGIN_DIR . '/_inc/include.php';


add_action( 'admin_menu', 'inject_admin' );





function inject_admin() {
    add_menu_page( 'WP Users Filtered', 'WP Users Filtered', 'manage_options', 'wp-listusersfiltered/listusersfiltered.php', 'list_table_users_filtered', '
dashicons-id-alt', 1  );
}




function list_table_users_filtered( ) {

    ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">


    <div class="alert alert-primary" role="alert">
        WP Users Filtered
    </div>
    <div class="container">
        <select class="custom-select luf-cmbFilter">
            <option selected disabled>Filter</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
        </select>

        <hr />

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">E-mail</th>
                <th scope="col">Role</th>
            </tr>
            </thead>
            <tbody>
    <?php
    //$users = get_users( [ 'role__in' => [ 'subscriber', 'subscriber', 'author' ] ] );
    $args = array(
        'role'         => '',
        'role__in'     => array(),
        'role__not_in' => array(),
        'meta_query'   => array(),
        'date_query'   => array(),
        'include'      => array(),
        'exclude'      => array(),
        'orderby'      => 'id',
        'order'        => 'asc',
    );
    $allusers =  get_users($args);
        foreach ($allusers as $usereach) {
            ?>
            <tr>

                <th scope="row"><?= $usereach->ID ?></th>
                <td><?= $usereach->user_nicename ?></td>
                <td><?= $usereach->user_email ?></td>
                <td><?= implode(', ', get_userdata($usereach->ID)->roles)  . "\n" ?></td>


            </tr>
            <?php
        }
            ?>
            </tbody>
        </table>
    </div>

            <!-- jQuery first, then Popper.js, then Bootstrap JS -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <?php
}

add_shortcode( 'listusersfiltered', 'list_table_users_filtered' );




