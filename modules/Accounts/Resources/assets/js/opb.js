$(document).ready(function () {
    "use strict";
         var orderlist=$('#opb_list').DataTable({ 
            responsive: false, 
            paging: true,
            order: [[1, 'desc']],
            "language": {
                "sProcessing":     lang.Processingod,
                "sSearch":         lang.search,
                "sLengthMenu":     lang.sLengthMenu,
                "sInfo":           lang.sInfo,
                "sInfoEmpty":      lang.sInfoEmpty,
                "sInfoFiltered":   lang.sInfoFiltered,
                "sInfoPostFix":    "",
                "sLoadingRecords": lang.sLoadingRecords,
                "sZeroRecords":    lang.sZeroRecords,
                "sEmptyTable":     lang.sEmptyTable,
                "oPaginate": {
                    "sFirst":      lang.sFirst,
                    "sPrevious":   lang.sPrevious,
                    "sNext":       lang.sNext,
                    "sLast":       lang.sLast
                },
                "oAria": {
                    "sSortAscending":  ":"+lang.sSortAscending+'"',
                    "sSortDescending": ":"+lang.sSortDescending+'"'
                },
                    "select": {
                            "rows": {
                                "_": lang._sign,
                                "0": lang._0sign,
                                "1": lang._1sign
                            }  
            },
                buttons: {
                        copy: lang.copy,
                        csv: lang.csv,
                        excel: lang.excel,
                        pdf: lang.pdf,
                        print: lang.print,
                        colvis: lang.colvis
                    }
            },
            'columnDefs': [ {
                    'targets': [0], 
                    'orderable': false,
                 }],
            dom: 'Blfrtip', 
            "lengthMenu": [[ 25, 50, 100, 150, 200, 500, -1], [ 25, 50, 100, 150, 200, 500, "All"]], 
            buttons: [  
                {extend: 'copy', className: 'btn-sm'}, 
                {extend: 'csv', title: 'ExampleFile', className: 'btn-sm',exportOptions: {columns: ':visible'}}, 
                {extend: 'excel', title: 'ExampleFile', className: 'btn-sm', title: 'exportTitle',exportOptions: {columns: ':visible'}}, 
                {extend: 'pdf', title: 'ExampleFile', className: 'btn-sm',exportOptions: {columns: ':visible'}}, 
                {extend: 'print', className: 'btn-sm',exportOptions: {columns: ':visible'}},
                {extend: 'colvis', className: 'btn-sm'}  
                
            ],
            "searching": true,
              "processing": true,
                     "serverSide": true,
                     "ajax":{
                        url :basicinfo.baseurl+"accounts/AccOpeningBalanceController/getOpeningBalanceList",
                        type: "post",
                        "data": function ( data ) {
                            data.csrf_test_name = $('#csrfhashresarvation').val();
                            data.financialyear = $('#financialyear').val();
                        }
                      },
                      "footerCallback": function(row, data, start, end, display) {
                        var api = this.api();
                
                        // Function to format the numbers as integers
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                
                        // Total over all pages
                        var totalDebit = api
                            .column(6)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                
                        var totalCredit = api
                            .column(7)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                
                        // Update the footer with the total sum
                        $(api.column(6).footer()).html(totalDebit.toFixed(2));
                        $(api.column(7).footer()).html(totalCredit.toFixed(2));
                    }
                });
                $('#filterordlist').click(function() {
                    var financialyear=$("#financialyear").val();
                    if(financialyear==''){
                        alert('Please enter Financial Year');
                        return false;
                        }
                    
                    orderlist.ajax.reload(null, false);
                });
                $('#filterordlistrst').click(function() {
                    $("#financialyear").val('').trigger('change');
                    orderlist.ajax.reload(null, false);
                });
                // var actionbtn='<button class="btn btn-danger pull-left" id="deleteorder" disabled="disabled">'+lang.Delete_Order+'</button>';
                // $("#tallorder_filter").append(actionbtn);
                // $('#deleteorder').click(function() {
                //    if (confirm(lang.Are_you_sure_you_want_to_delete) == true) {
                //         var totalchecked=$('input.singleorder:checkbox:checked').length;
                //         var orderid = [];
                //         if(totalchecked >0){
                //         $("input[name='checkasingle']:checked").each(function(){
                //             orderid.push(this.value);
                //         });
                //          var url = basicinfo.baseurl+'ordermanage/order/deletecompleteorder';
                //          var csrf = $('#csrfhashresarvation').val();
                //          $.ajax({
                //                  type: "POST",
                //                  url: url,
                //                  data:{_token:csrf,orderid:orderid},
                //                  success: function(data) {
                //                      if(data==1){
                //                          $(".singleorder").prop("checked", false);
                //                          $("#deleteorder").attr('disabled', 'disabled');
                //                          orderlist.ajax.reload(null, false);
                //                          swal(lang.success, lang.Successfully_Deleted, "success");
                //                      }else{
                //                          swal(lang.invalid, lang.Something_Wrong, "warning");
                //                          }
                //                 }
                //             });
                //         }
                //     } else {
                      
                //     }
               
                // });
                
                
    });