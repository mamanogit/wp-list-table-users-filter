<div class="wrap">
    <h1 class="wp-heading-inline">
        WP Users Filtered
    </h1>

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
                <option selected disabled value=""> <?php echo  esc_html__( 'Select role', 'textdomain' ) ?></option>
                <?php foreach ($all_roles as $roleeach): ?>
                    <option value="<?php echo $roleeach['name'] ?>"><?php echo $roleeach['name'] ?></option>
                <?php
                endforeach; ?>
            </select>
            <br />
        </div>

        <div id="divAppend">

        </div>
        <input type="hidden" name="myOrder" id="myOrder" value="asc" />

    </div>
</div>