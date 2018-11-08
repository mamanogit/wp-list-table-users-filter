<?php 

add_action( "wp_ajax_myaction", "so_wp_ajax_function" );
add_action( "wp_ajax_nopriv_myaction", "so_wp_ajax_function" );

function so_wp_ajax_function(){
if(isset($_POST['role'])){
    $role = $_POST['role'];
}else{
    $role = '';
}



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
<?php
    
    wp_die();
}
?>




