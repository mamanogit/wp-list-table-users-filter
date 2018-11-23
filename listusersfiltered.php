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
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_luf_ajax_scripts' ));
		//
		add_action('wp_ajax_luf_function', array( $this, 'ajax_luf_function'));
		add_action('wp_ajax_nopriv_luf_function', array( $this, 'ajax_luf_function'));

        //Includes
        require_once LUF_PLUGIN_DIR . '/includes/include.php';

        add_action( 'admin_menu', function () {
        add_menu_page( 'WP Users Filtered', 'WP Users Filtered', 'manage_options', 'wp-listusersfiltered',   array($this, 'list_table_users_filtered'), 'dashicons-id-alt', 1  );
        } );
	}

	//EnqueueScripts
	public function enqueue_luf_ajax_scripts() {
        wp_register_script( 'ajaxHandle',plugins_url( 'ajaxSend.js', __FILE__), array( 'jquery'), '', true );
        wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )) );
        wp_enqueue_script( 'ajaxHandle' );


	}

    public function list_table_users_filtered() {
       // $this->enqueue_luf_ajax_scripts();

        include dirname( __FILE__ ) . '/views/viewMain.php';
}

   public function ajax_luf_function(){
       $wpgenListTable = new WPGEN_List_Table();
       $wpgenListTable->prepare_items();

       include dirname( __FILE__ ) . '/views/_viewTable.php';

       wp_die();
    }

} //endclass

new LUF();

//////////END LUF /////////////

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
error_reporting( ~E_NOTICE );
/**
 * Create a new table class that will extend the WP_List_Table
 */
class WPGEN_List_Table extends WP_List_Table
{

    public function __construct() {

        global $status, $page;

        //Set parent defaults
        parent::__construct(
            array(
                //singular name of the listed records
                'singular'  => '',
                //plural name of the listed records
                'plural'    => '',
                //does this table support ajax?
                'ajax'      => true
            )
        );
    }

    public function display() {

       echo wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

        echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

        parent::display();
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
            'paged'          =>  isset($_POST['args']['paged']) ? max(0, intval($_POST['args']['paged'] -1) * 10) : 0,
            // Set ordering values if needed (useful for AJAX)
            'orderby'   => ! empty( $_POST['args']['orderby'] ) && '' != $_POST['args']['orderby']? $_POST['args']['orderby'] : 'nicename',
            'order'     => ! empty( $_POST['args']['order'] ) && '' != $_POST['args']['order'] ? $_POST['args']['order'] : 'asc'

    ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }


    public function get_pagenum() {
        $pagenum = isset( $_POST['args']['paged'] ) ? absint( $_POST['args']['paged'] ) : 0;
        if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] ) {
            $pagenum = $this->_pagination_args['total_pages'];
        }
        return max( 1, $pagenum );
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
            $role = array();
        }

        if(isset($_POST['args']['order'])){
            $order = $_POST['args']['order'];
        }else{
            $order = 'asc';
        }

        if(isset($_POST['args']['orderby'])){
            $orderby = $_POST['args']['orderby'];
        }else{
            $orderby = 'nicename';
        }

        $args = array(
            'role'         => '',
            'role__in' => $role,
            'role__not_in' => array(),
            'meta_query'   => array(),
            'date_query'   => array(),
            'include'      => array(),
            'exclude'      => array(),
           // 'orderby'      => $orderby,
           // 'order'        => $order,
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
        if(!empty($_POST['args']['orderby'] ))
        {
            $orderby = $_POST['args']['orderby'] ;
        }
        // If order is set use this as the order
        if(!empty($_POST['args']['order'] ))
        {
            $order = $_POST['args']['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }
}
