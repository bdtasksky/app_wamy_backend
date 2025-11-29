$(document).ready(function () {
    "use strict";
    var getPredefinedSettingList = window.appData.getPredefinedSettingList; 
    var voucherlist = $('#predefined_list').DataTable({
        responsive: false, 
        paging: true,
        order: [[1, 'desc']],
        "language": {
            "sProcessing": lang.Processingod,
            "sSearch": lang.search,
            "sLengthMenu": lang.sLengthMenu,
            "sInfo": lang.sInfo,
            "sInfoEmpty": lang.sInfoEmpty,
            "sInfoFiltered": lang.sInfoFiltered,
            "sInfoPostFix": "",
            "sLoadingRecords": lang.sLoadingRecords,
            "sZeroRecords": lang.sZeroRecords,
            "sEmptyTable": lang.sEmptyTable,
            "oPaginate": {
                "sFirst": lang.sFirst,
                "sPrevious": lang.sPrevious,
                "sNext": lang.sNext,
                "sLast": lang.sLast
            },
            "oAria": {
                "sSortAscending": ":" + lang.sSortAscending + '"',
                "sSortDescending": ":" + lang.sSortDescending + '"'
            },
            "select": {
                "rows": {
                    "_": lang._sign,
                    "0": lang._0sign,
                    "1": lang._1sign
                }
            }
        },
        "columnDefs": [{
            'targets': [0], 
            'orderable': false
        }],
        dom: 'Blfrtip',
        "lengthMenu": [[25, 50, 100, 150, 200, 500, -1], [25, 50, 100, 150, 200, 500, "All"]],
        buttons: [  
            { extend: 'copy', className: 'btn-sm' },
            { extend: 'csv', title: 'ExampleFile', className: 'btn-sm', exportOptions: { columns: ':visible' } },
            { extend: 'excel', title: 'ExampleFile', className: 'btn-sm', exportOptions: { columns: ':visible' } },
            { extend: 'pdf', title: 'ExampleFile', className: 'btn-sm', exportOptions: { columns: ':visible' } },
            { extend: 'print', className: 'btn-sm', exportOptions: { columns: ':visible' } },
            { extend: 'colvis', className: 'btn-sm' }
        ],
        "searching": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: getPredefinedSettingList,
            type: "post",
            "data": function (data) {
                data.csrf_test_name = $('#csrfhashresarvation').val();
            }
        }
    });
});
