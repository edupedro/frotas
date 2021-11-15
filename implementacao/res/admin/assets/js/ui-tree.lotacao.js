/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/

handleJstreeAjax=function() {
    $("#jstree-ajax").jstree( {
        core: {
            themes: {
                responsive: !1
            }
            , check_callback:!0, data: {
                url:function(e) {
                    return"#"===e.id?"orgao_busca_inicio_json.php": ""+e.original.file
                }
                , data:function(e) {
                    return {
                        id: e.id
                    }
                }
                , dataType:"json"
            }
        }
        , types: {
            "default": {
                icon: "fa fa-folder text-warning fa-lg"
            }
            , file: {
                icon: "fa fa-file text-warning fa-lg"
            }
        }
        , plugins:["dnd", "state", "types"]
    }
    )
}

,
TreeView=function() {
    "use strict";
    return {
        init:function() {
            handleJstreeAjax()
        }
    }
}

();