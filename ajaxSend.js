

jQuery('#cmbRole').on('change', function(){
    var args = [];
    renderListAjax(args)
});

function callTableActionsAjax(e) {
    var urlcurrent = e.currentTarget.href;
    var urlquery = new URL(urlcurrent);
    urlquery  = urlquery.search.substr(1);

    //console.log(urlquery);

    var argsOrder = "";
    if(jQuery('#myOrder').val() === 'asc'){
        argsOrder = "desc";
        jQuery('#myOrder').val(argsOrder);
    }else{
        argsOrder = "asc";
        jQuery('#myOrder').val(argsOrder);
    }

    var args = {
        order: argsOrder,
        orderby: searchEaQuery( urlquery, 'orderby' ) || 'nicename'
    };

    renderListAjax(args);
};

function callTablePaginationAjax(e) {
    var urlcurrent = e.currentTarget.href;
    var urlquery = new URL(urlcurrent);
    urlquery  = urlquery.search.substr(1);


    var args = {
        paged: searchEaQuery( urlquery, 'paged' ) || '1',
    };

    renderListAjax(args);
};

 function searchEaQuery( query, variable ) {
    var vars = query.split("&");
    for ( var i = 0; i <vars.length; i++ ) {
        var pair = vars[ i ].split("=");
        if ( pair[0] == variable )
            return pair[1];
    }
    return false;
};

function renderListAjax(args){
    console.log(args);

    var data = {
        'action': 'luf_function',
        'role': jQuery('#cmbRole').val(),
        'args': args
    };
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: data,
        success: function (response) {
            jQuery('#divAppend').html(response);
        },
        error: function (error) {
            console.log(error);
        }
    })
}