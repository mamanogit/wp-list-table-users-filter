<?php
/*
Plugin Name: WP List Users Filtered
Description: List users filtered
Author: skarioasto
Author URI: https://github.com/Sonecaa
Version: 0.1.0
License: MIT
*/


class LUF {
    
   
    
	public function __construct(){
		add_action('plugins_loaded', array($this, 'init'), 2);
	}
	public function init(){
         
        define( 'LUF_PLUGIN_DIR', __DIR__ );
        define( 'LUF_PLUGIN_URL', plugins_url(null, __FILE__) );
		//Add Ajax Actions
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_luf_ajax_scripts' ));
		add_action('wp_ajax_luf_function', array( $this, 'ajax_luf_function'));
		add_action('wp_ajax_nopriv_luf_function', array( $this, 'ajax_luf_function'));
        //Includes
        require_once LUF_PLUGIN_DIR . '/_inc/include.php';
        
        add_action( 'admin_menu', function () {
        add_menu_page( 'WP Users Filtered', 'WP Users Filtered', 'manage_options', 'wp-listusersfiltered/listusersfiltered.php',   array($this, 'list_table_users_filtered'), 'dashicons-id-alt', 1  );
        } );
        
	}
    
    
	//EnqueueScripts
	public function enqueue_luf_ajax_scripts() {
	    //wp_register_script( 'genre-ajax-js', plugin_dir_url(__FILE__). 'genre.js', array( 'jquery' ), '', true );
	    //wp_localize_script( 'genre-ajax-js', 'ajax_genre_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	    //wp_enqueue_script( 'genre-ajax-js' );
        
        wp_register_script( 'ajaxHandle',plugins_url( 'ajaxSend.js', __FILE__), array( 'jquery'), '', true );
        wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )) );
        wp_enqueue_script( 'ajaxHandle' );

	}
    
  

    
    public function list_table_users_filtered() {
        $this->enqueue_luf_ajax_scripts();
        ?>
    


    <h2 class="wp-heading-inline">
        WP Users Filtered
    </h2>
    
    <div>
       <?php    
    global $wp_roles;
    $all_roles = $wp_roles->roles;
    //echo "<pre>" . var_dump($all_roles); "</pre>";
        ?>
    </div>


        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <!-- onchange="callScript(this.value)" -->
                <select class="" id="cmbRole" >
                    <option selected disabled>Select role</option>
                    <?php foreach ($all_roles as $roleeach): ?>
                        <option value="<?php echo $roleeach['name'] ?>"><?php echo $roleeach['name'] ?></option>
                    <?php
                    endforeach; ?>
                </select>
                <br />
            </div>

            <div id="divAppend">
                
            </div>   
    
    </div>
   
    <?php
}
  
    
    
    
   public function ajax_luf_function(){
       $wpgenListTable = new WPGEN_List_Table();
       $wpgenListTable->prepare_items();
       ?>
       <div class="wrap">
           <?php $wpgenListTable->display(); ?>
       </div>
       <?php
    
    wp_die();
    }

    
} //endclass



new LUF();


// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class WPGEN_List_Table extends WP_List_Table
{


    function __construct() {

        global $status, $page;

        //Set parent defaults
        parent::__construct(
            array(
                //singular name of the listed records
                'singular'  => 'user',
                //plural name of the listed records
                'plural'    => 'users',
                //does this table support ajax?
                'ajax'      => true
            )
        );

    }


    function display() {

        wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

        echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

        parent::display();
    }

    function ajax_response() {

        check_ajax_referer( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

        $this->prepare_items();

        extract( $this->_args );
        extract( $this->_pagination_args, EXTR_SKIP );

        ob_start();
        if ( ! empty( $_REQUEST['no_placeholder'] ) )
            $this->display_rows();
        else
            $this->display_rows_or_placeholder();
        $rows = ob_get_clean();

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $response = array( 'rows' => $rows );
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;

        if ( isset( $total_items ) )
            $response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

        if ( isset( $total_pages ) ) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n( $total_pages );
        }

        die( json_encode( $response ) );
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage,
            'total_pages'   => ceil( $totalItems / $perPage ),
            // Set ordering values if needed (useful for AJAX)
            'orderby'   => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'nicename',
            'order'     => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'

    ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id'          => 'ID',
            'nicename'       => 'Name',
            'email' => 'E-mail',
            'role'        => 'Role',
        );
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('nicename' => array('nicename', false));
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        if(isset($_POST['role'])){
            $role = $_POST['role'];
        }else{
            $role = '';
        }
        $args = array(
            'role'         => '',
            'role__in'     => $role,
            'role__not_in' => array(),
            'meta_query'   => array(),
            'date_query'   => array(),
            'include'      => array(),
            'exclude'      => array(),
            'orderby'      => 'id',
            'order'        => 'asc',
        );
        $allusers =  get_users($args);
        $data = array();
        foreach ($allusers as $eauser){
            $data[] = array(
                'id'          => $eauser->ID,
                'nicename'       => $eauser->user_nicename ,
                'email' => $eauser->user_email ,
                'role'        => implode(', ', get_userdata($eauser->ID)->roles)  . "\n",
            );
        }



        return $data;
    }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'nicename':
            case 'email':
            case 'role':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'nicename';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }
}



function _ajax_fetch_custom_list_callback() {

    $wpgenListTable = new WPGEN_List_Table();
    $wpgenListTable->ajax_response();
}
add_action('wp_ajax__ajax_fetch_custom_list', '_ajax_fetch_custom_list_callback');

function fetch_ts_script() {
?>

<script type="text/javascript">

(function($) {

    list = {

        /**
         * Register our triggers
         *
         * We want to capture clicks on specific links, but also value change in
         * the pagination input field. The links contain all the information we
         * need concerning the wanted page number or ordering, so we'll just
         * parse the URL to extract these variables.
         *
         * The page number input is trickier: it has no URL so we have to find a
         * way around. We'll use the hidden inputs added in TT_Example_List_Table::display()
         * to recover the ordering variables, and the default paged input added
         * automatically by WordPress.
         */
        init: function() {

            // This will have its utility when dealing with the page number input
            var timer;
            var delay = 500;

            // Pagination links, sortable link
            $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
                // We don't want to actually follow these links
                e.preventDefault();
                // Simple way: use the URL to extract our needed variables
                var query = this.search.substring( 1 );

                var data = {
                    paged: list.__query( query, 'paged' ) || '1',
                    order: list.__query( query, 'order' ) || 'asc',
                    orderby: list.__query( query, 'orderby' ) || 'nicename'
                };
                list.update( data );
            });

            // Page number input
            $('input[name=paged]').on('keyup', function(e) {

                // If user hit enter, we don't want to submit the form
                // We don't preventDefault() for all keys because it would
                // also prevent to get the page number!
                if ( 13 == e.which )
                    e.preventDefault();

                // This time we fetch the variables in inputs
                var data = {
                    paged: parseInt( $('input[name=paged]').val() ) || '1',
                    order: $('input[name=order]').val() || 'asc',
                    orderby: $('input[name=orderby]').val() || 'nicename'
                };

                // Now the timer comes to use: we wait half a second after
                // the user stopped typing to actually send the call. If
                // we don't, the keyup event will trigger instantly and
                // thus may cause duplicate calls before sending the intended
                // value
                window.clearTimeout( timer );
                timer = window.setTimeout(function() {
                    list.update( data );
                }, delay);
            });
        },

        /** AJAX call
         *
         * Send the call and replace table parts with updated version!
         *
         * @param    object    data The data to pass through AJAX
         */
        update: function( data ) {
            $.ajax({
                // /wp-admin/admin-ajax.php
                url: ajaxurl,
                // Add action and nonce to our collected data
                data: $.extend(
                    {
                        _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                        action: '_ajax_fetch_custom_list',
                    },
                    data
                ),
                // Handle the successful result
                success: function( response ) {

                    // WP_List_Table::ajax_response() returns json
                    var response = $.parseJSON( response );

                    // Add the requested rows
                    if ( response.rows.length )
                        $('#the-list').html( response.rows );
                    // Update column headers for sorting
                    if ( response.column_headers.length )
                        $('thead tr, tfoot tr').html( response.column_headers );
                    // Update pagination for navigation
                    if ( response.pagination.bottom.length )
                        $('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
                    if ( response.pagination.top.length )
                        $('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );

                    // Init back our event handlers
                    list.init();
                }
            });
        },

        /**
         * Filter the URL Query to extract variables
         *
         * @see http://css-tricks.com/snippets/javascript/get-url-variables/
         *
         * @param    string    query The URL query part containing the variables
         * @param    string    variable Name of the variable we want to get
         *
         * @return   string|boolean The variable value if available, false else.
         */
        __query: function( query, variable ) {

            var vars = query.split("&");
            for ( var i = 0; i <vars.length; i++ ) {
                var pair = vars[ i ].split("=");
                if ( pair[0] == variable )
                    return pair[1];
            }
            return false;
        },
    }

// Show time!
    list.init();

})(jQuery);

</script>
    <?php
}
add_action('admin_footer', 'fetch_ts_script');



