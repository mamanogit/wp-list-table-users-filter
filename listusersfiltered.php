<?php
/*
Plugin Name: WP List Users Filtered
Description: List users filtered
Author: skarioasto
Author URI: https://github.com/Sonecaa
Version: 0.1.0
License: MIT
*/


function create_page_users()
{

    $post_title = "WP Users Filtered";

    // Create post object
    $new_page = array(
        'post_type'     => 'page',
        'post_title'    => $post_title,
        'post_status'   => 'publish',
        'post_author'   => 1,
    );

    // Insert the post into the database
    wp_insert_post( $new_page );
    exit();
};

function list_table_users_filtered( $content ) {

    // Check if we're inside the main loop in a single post page.
    if ( is_page() ) {
      $page =  get_page_by_title( 'WP Users Filtered' );

      if($page != null){
          $result = "tem a pagins";
      }else{
          $result = "nao tem a pagina";
      }

        return $content . $result;
    }

    return $content;
}

add_filter( 'the_content', 'list_table_users_filtered' );