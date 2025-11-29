
"use strict";
$(document).on('click', '.v_view', function(e) {
    e.preventDefault();  // Prevent the default link action

    var vid = $(this).data('id');  // Get voucher ID 
    var vdate = $(this).data('vdate');
    var csrf = $('#csrfhashresarvation').val();  // CSRF token
    var voucherDetailsUrl = window.appData.voucherDetailsUrl; 


    // Perform AJAX request directly in this method
    $.ajax({
        type: 'POST',
        url: voucherDetailsUrl,
        dataType: 'JSON',
        data: { vid: vid, vdate: vdate, csrf_test_name: csrf },
        success: function(res) {
                $('#all_vaucher_view').html(res.data);
                // Set the PDF download link
                // $("a#pdfDownload").prop("href", basicinfo.baseurl + res.pdf);
                // Show the modal
                $('#allvaucherModal').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Error occurred while fetching voucher details.');
        }
    });
});



$(document).ready(function () {
    // Your code for loadTransactions and other functions

  
    // Initialize loadTransactions
    loadTransactions(1);  // Load data for the first page when document is ready

    // Handle Search button click
    $('#filter-form').on('click', function (e) {
        e.preventDefault();
        loadTransactions(1);  // Load transactions for page 1 when filter form is submitted
    });
});

function renderPagination(totalRows, rowsPerPage, currentPage) {
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    let paginationHtml = '';

    // Add "Previous" button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}">Previous</a>
        </li>`;

    // Add numbered page links
    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
            </li>`;
    }

    // Add "Next" button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}">Next</a>
        </li>`;

    // Insert into pagination container
    $('#pagination').html(paginationHtml);

    // Add click event to pagination links
    $('.page-link').on('click', function () {
        const page = $(this).data('page');
        if (page >= 1 && page <= totalPages) {
            loadTransactions(page);
        }
    });
}



function loadTransactions(page = 1) {
    var voucher_type = $('#voucher_type option:selected').val();
    var row = $('#row option:selected').val() || 10;  // Default to 10 rows
    var status = $('#status option:selected').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var branch = $('#branch').val();
    var csrf = $('#csrfhashresarvation').val();  // Make sure to use the correct CSRF token element
    var voucher_no = $('#voucher_no').val();
    var project_id = $('#project_id').val();
    var schedule_status = $('#schedule_status').val();

    var getPendingListUrl = window.appData.getPendingListUrl; 

    
    $.ajax({
        url: getPendingListUrl,
        method: "POST",
        data: {
            csrf_test_name: csrf,
            voucher_type: voucher_type,
            row: row,
            status: status,
            from_date: from_date,
            to_date: to_date,
            page: page,
            branch: branch,
            voucher_no: voucher_no,
            project_id: project_id,
            schedule_status: schedule_status,
        },
        success: function (response) {

       
            let data = response;
           
            let transactions = data.transactions;
            let total_rows = data.total_rows;
            let rowsHtml = '';

            if (!transactions || transactions.length === 0) {
                $('#ledger-body').empty();
                rowsHtml += '<tr class="text-center"><td colspan="7">No Data Found</td></tr>';
            } else {
                $.each(transactions, function (index, transaction) {
                console.log(transaction);
                    rowsHtml += '<tr>';
                     rowsHtml += '<td><input type="checkbox" name="voucherId[]" value="'+transaction.voucher_master_id+'" /></td>'
                    rowsHtml += '<td>' + ((page - 1) * row + ++index) + '</td>';  // Adjust row numbering
                    rowsHtml += '<td>';
                    rowsHtml += '<a href="javascript:" data-id="' + transaction.voucher_master_id + '" '
                        + 'data-vdate="' + transaction.VoucherDate + '" '
                        + 'class="v_view" style="margin-right:10px" title="View Voucher">'
                        + transaction.VoucherNumber + '</a></td>';
                    rowsHtml += '<td>' + transaction.VoucherDate + '</td>';
                    rowsHtml += '<td>' + (transaction.Remarks || '') + '</td>';
                    rowsHtml += '<td class="text-end">' + (transaction.TranAmount != null ? transaction.TranAmount : '0.00') + '</td>';
                    rowsHtml += '<td class="text-center">';
                    rowsHtml += (transaction.IsApprove == 1 ? '<span class="label label-success">Approved</span>' : '<span class="label label-danger">Pending</span>');
                    rowsHtml += '</td>';

                    

                    rowsHtml += '<td class="text-center">';

                    // Add buttons for actions based on transaction status
                    // rowsHtml += '<a href="' + baseUrl.replace("__ID__", transaction.voucher_master_id) + '" ' +
                    //                 'class="btn btn-xs btn-success" style="margin-right:10px" title="Edit Voucher">' +
                    //                 '<i class="fa fa-pencil"></i></a>';

                    if (transaction.IsApprove == 0 && transaction.IsYearClosed == 0) {
                        rowsHtml += '<a href="' + baseUrl.replace("__ID__", transaction.voucher_master_id) + '" ' +
                                    'class="btn btn-xs btn-success" style="margin-right:10px" title="Edit Voucher">' +
                                    '<i class="fa fa-pencil"></i></a>';
                        rowsHtml += '<button class="btn btn-xs btn-danger v_delete" style="margin-right:10px" '
                            + 'data-id="' + transaction.voucher_master_id + '" title="Delete Voucher">'
                            + '<i class="fa fa-trash"></i></button>';
                    } else if (transaction.IsApprove == 1 && transaction.IsYearClosed == 0) {
                        rowsHtml += '<button class="btn btn-xs btn-success v_reverse" style="margin-right:10px" '
                            + 'data-id="' + transaction.voucher_master_id + '" title="Reverse Voucher">'
                            + '<i class="fa fa-undo"></i></button>';
                    }
                    rowsHtml += '</td></tr>';
                });
            }

            $('#ledger-body').html(rowsHtml);
            renderPagination(total_rows, row, page);  // Render pagination after the transactions
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
}


$('#selectall').on('click', function() {
    var rows = $('#pendingvouchers tbody tr'); 
    $('input[type="checkbox"]', rows).prop('checked', this.checked); 
});



function approveVouchers() {

    var csrf = $('#csrfhashresarvation').val();
    var approveVoucherUrl = window.appData.approveVoucherUrl; 

    $('#selectall').on('click', function() {
        var rows = $('#pendingvouchers tbody tr');
        $('input[type="checkbox"]', rows).prop('checked', this.checked); 
    });

    // $('#approveBtn').on('click', function() {

        var selectedIds = [];
        // Skip the #selectall checkbox from checked list
        $('input[type="checkbox"]:checked').not('#selectall').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one voucher to approve.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: approveVoucherUrl,
            data: {
                csrf_test_name: csrf,
                voucher_ids: selectedIds
            },
            success: function(response) {


                if (response.status == 'success') {
                    alert('Vouchers approved successfully.');
                    location.reload();
                } else {
                    alert('Failed to approve vouchers.');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
                alert('An error occurred. Please try again later.');
            }
        });
    // });
}
"use strict";
$(document).on('click', '.v_delete', function(e) {
  e.preventDefault();
  var vno = $(this).attr('data-id'); // Get voucher ID from the button's data attribute
  var csrf = $('#csrfhashresarvation').val();
  var voucherDeleteUrl = window.appData.voucherDeleteUrl; 

    // Use SweetAlert2 to confirm the action
    Swal.fire({
        title: 'Are you sure?',
        text: "You will not be able to recover this voucher!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
    
        $.ajax({
            url : voucherDeleteUrl,
            type: "POST",
            dataType: "json",
            data: { vno: vno, csrf_test_name: csrf },
            success: function(data)
            {   
                if(data.status == "success") {
                    location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error getting data from ajax');
            }
        });
        } else {
            // If the user clicked 'No, keep it', nothing happens
            console.log("Voucher Delete cancelled.");
        }
    });
});
