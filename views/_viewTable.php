
<form id="users-filter" method="get">
    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $wpgenListTable->display(); ?>
</form>
<script type="text/javascript">
    jQuery('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
        // We don't want to actually follow these links
        e.preventDefault();

        callTableActionsAjax();

        // Pagination links, sortable link
        function callTableActionsAjax() {
            // Simple way: use the URL to extract our needed variables
            var urlcurrent = e.currentTarget.href;
            var urlquery = new URL(urlcurrent);
            urlquery  = urlquery.search.substr(1);

            console.log(urlquery);
            var data = {
                paged: list.__query( urlquery, 'paged' ) || '1',
                order: list.__query( urlquery, 'order' ) || 'asc',
                orderby: list.__query( urlquery, 'orderby' ) || 'nicename'
            };
            console.log(data);


            list.update( data );
        };
    });
</script>