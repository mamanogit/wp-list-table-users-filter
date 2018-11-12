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
      
 wp_head();
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
        <select class="custom-select luf-cmbFilter" id="cmbRole" onchange="callScript(this.value)">
             <option selected disabled>Select role</option>
            <?php foreach ($all_roles as $roleeach): ?>         
            <option value="<?= $roleeach['name'] ?>"><?=  $roleeach['name'] ?></option>
            <?php
                endforeach; ?>
        </select
            

        <hr />
        
            <div id="divAppend">
                
            </div>   
    
    </div>
   
    <?php
}
  
    
    
    
   public function ajax_luf_function(){
       global $wpdb;
            if(isset($_POST['role'])){
                    $role = $_POST['role'];
            }else{
                $role = '';
            }


//echo "Role selected: " . var_dump($role);
?>
        
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
<?php
    
    wp_die();
    }

    
} //endclass



new LUF();
?>