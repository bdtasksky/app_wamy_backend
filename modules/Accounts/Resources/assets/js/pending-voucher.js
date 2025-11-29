$(document).ready(function () {
    "use strict";
         var voucherlist=$('#voucher_list').DataTable({ 
            responsive: false, 
            paging: true,
            order: [[2, 'desc']],
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
                    'targets': [0,1,6], 
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
                        url :basicinfo.baseurl+"accounts/AccPendingVoucherController/getPendingVoucherList",
                        type: "post",
                        "data": function ( data ) {
                            data.csrf_test_name = $('#csrfhashresarvation').val();
                            data.voucher_type = $('#voucher_type').val();
                        }
                      },
                      
                });
                $('#filterordlist').click(function() {
                    var voucher_type=$("#voucher_type").val();
                    if(voucher_type==''){
                        alert('Please enter Vocuher Type');
                        return false;
                        }
                    
                    voucherlist.ajax.reload(null, false);
                });

                $('#filterordlistrst').click(function() {
                    $("#voucher_type").val('').trigger('change');
                    voucherlist.ajax.reload(null, false);
                }); 
                                // Handle 'Select All' functionality
                $('#selectall').on('click', function() {
                    var rows = voucherlist.rows({ 'search': 'applied' }).nodes();
                    $('input[type="checkbox"]', rows).prop('checked', this.checked);
                });

                // Submit form and collect selected voucher IDs
                $('#voucherApprovalForm').on('submit', function(e) {
                    e.preventDefault();  // Prevent the default form submission

                    var selectedVouchers = [];
                    $('input[name="voucherId[]"]:checked').each(function() {
                        selectedVouchers.push($(this).val());
                    });

                    if (selectedVouchers.length === 0) {
                        alert('Please select at least one voucher for approval.');
                        return false;
                    }

                    // Send the selected voucher IDs via Ajax
                    $.ajax({
                        url: $(this).attr('action'), // Form action URL
                        type: 'POST',
                        data: {
                            voucherId: selectedVouchers,  // Send the selected voucher IDs
                            csrf_test_name: $('#csrfhashresarvation').val() // Include CSRF token
                        },
                        success: function(response) {
                            console.log(response);
                            // Handle the response from the server
                            if (response.status === 'success') {
                                // Display success message
                                alert(response.message);
                            } else {
                                // Display error message
                                alert("Error: " + response.message);
                            }
                            voucherlist.ajax.reload();  // Reload the DataTable
                        },
                        error: function(xhr, status, error) {
                            // Handle any errors
                            alert('An error occurred: ' + error);
                        }
                    });
                });                
    });