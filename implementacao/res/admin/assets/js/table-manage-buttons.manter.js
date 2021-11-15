/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/

var handleDataTableButtons=function() {
    "use strict";
    0!==$("#data-table").length&&$("#data-table").DataTable( {
        dom:"Bfrtip", buttons:[ {
            extend: "copy", className: "btn-sm"
        }
        , {
            extend: "csv", className: "btn-sm"
        }
        , {
            extend: "excel", className: "btn-sm"
        }
        , {
            extend: "pdf", className: "btn-sm"
        }
        , {
            extend: "print", className: "btn-sm"
        }
        ], responsive:!0,
        "oLanguage": {
            "sProcessing": "Aguarde enquanto os dados são carregados ...",
            "sLengthMenu": "Mostrar _MENU_ ",
            "sZeroRecords": "Nenhum registro correspondente a busca",
            "sInfoEmtpy": "Exibindo 0 a 0 de 0 registros",
            "sInfo": "Exibindo de _START_ a _END_ de _TOTAL_ registros",
            "sInfoFiltered": "",
            "sSearch": "Procurar ",
            "oPaginate": {
               "sFirst":    "Primeiro",
               "sPrevious": "Anterior",
               "sNext":     "Próximo",
               "sLast":     "Último"
            }
         },
         "iDisplayLength": 10,
         "aLengthMenu": [[10, 30, 50, 100, -1], [5, 10, 30, 50, 100, "Todos"]]
    })
}

,
TableManageButtons=function() {
    "use strict";
    return {
        init:function() {
            handleDataTableButtons()
        }
    }
}

();