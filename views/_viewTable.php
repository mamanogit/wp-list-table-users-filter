
<form id="users-filter" method="get">
    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
    <?php
        $pagedView = (isset($_POST['args']['paged'])) ? $_POST['args']['paged'] : '';
    ?>
    <input type="hidden" name="page" id="paged" value="<?php echo $pagedView ?>" />

    <?php $wpgenListTable->display(); ?>

</form>
<script type="text/javascript">
    jQuery('.manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
        // We don't want to actually follow these links
        e.preventDefault();
        callTableActionsAjax(e);
    });

    jQuery('.tablenav-pages a').on('click', function(e) {
        // We don't want to actually follow these links
        e.preventDefault();
        callTablePaginationAjax(e);
    });

    jQuery('#paged').on('keyup', function (e) {
        e.preventDefault();
        if (13 == e.which){
            callTablePaginationAjax(e);
        }
    });


</script>