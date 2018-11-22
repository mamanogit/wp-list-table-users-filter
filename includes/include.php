<?php

function add_my_stylesheet()
{
wp_enqueue_style('style1', LUF_PLUGIN_URL . '/includes/listusersfiltered.css');
}

add_action('admin_print_styles', 'add_my_stylesheet');
?>