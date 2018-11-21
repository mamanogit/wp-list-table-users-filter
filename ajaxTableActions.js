

(function($) {
    $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
        alert('teste');
        console.log('teste');
    });

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
                    orderby: $('input[name=orderby]').val() || 'title'
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
                        action: 'fetch_custom_list_callback',
                    },
                    data
                ),
                // Handle the successful result
                success: function( response ) {
                    // WP_List_Table::ajax_response() returns json
                    console.log(response);
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