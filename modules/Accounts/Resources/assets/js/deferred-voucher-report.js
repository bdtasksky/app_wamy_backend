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
    var voucher_no = $('#voucher_no').val();
    var csrf = $('#csrfhashresarvation').val();  // Make sure to use the correct CSRF token element
    var project_id = $('#project_id').val();

    var getListUrl = window.appData.getListUrl; 
    
    $.ajax({
        url: getListUrl,
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
        },
        success: function (response) {

       
            let data = response;
           
            let transactions = data.transactions;
            let total_rows = data.total_rows;
            let rowsHtml = '';
            console.log(transactions);
            if (!transactions || transactions.length === 0) {
                $('#ledger-body').empty();
                rowsHtml += '<tr class="text-center"><td colspan="7">No Data Found</td></tr>';
            } else {
                $.each(transactions, function (index, transaction) { 
               
                    rowsHtml += '<tr>';
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
                    rowsHtml += '<a href="javascript:" data-id="' + transaction.voucher_master_id + '" data-vdate="' + transaction.VoucherDate + '" '
                        + 'data-type="' + transaction.VoucharTypeId + '" '
                        + 'class="btn btn-xs btn-info v_view" style="margin-right:10px" title="View Voucher">'
                        + '<i class="fa fa-eye"></i></a>';

                    rowsHtml += '<a href="javascript:" data-id="' + transaction.voucher_master_id + '" data-vdate="' + transaction.VoucherDate + '" '
                        + 'data-type="' + transaction.VoucharTypeId + '" '
                        + 'class="btn btn-xs btn-info v_c_view" style="margin-right:10px" title="View Voucher Children">'
                        + '<i class="fa fa-list"></i></a>';

                        // *** END OF NEW CODE ***
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
"use strict";
$(document).on('click', '.v_c_view', function(e) {
    e.preventDefault();  // Prevent the default link action

    var vid = $(this).data('id');  // Get voucher ID 
    var vdate = $(this).data('vdate');
    var csrf = $('#csrfhashresarvation').val();  // CSRF token
    var voucherDetailsChildrenUrl = window.appData.voucherDetailsChildrenUrl; 


    // Perform AJAX request directly in this method
    $.ajax({
        type: 'POST',
        url: voucherDetailsChildrenUrl,
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

"use strict";
function printVaucher(modald) {
    var divName = "vaucherPrintArea";
    var printContents = document.getElementById(modald).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    setTimeout(function() {
         // $('#'+modald).modal().hide();;
         // $("#"+modald + " .close").click();
            location.reload();
           }, 100);
}
// Delete voucher
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
// reverse voucher
//   "use strict";
//   $(document).on('click', '.v_reverse', function(e) {
//     e.preventDefault();
//     var vno = $(this).attr('data-id'); // Get voucher number from data attribute
//     var conf = confirm('are_you_sure');
//     var csrf = $('#csrfhashresarvation').val();
//     var voucherReverseUrl = window.appData.voucherReverseUrl; 
   
//     // console.log(vno);
//     // return false;

//     if(conf) {
//         $.ajax({
//             url : voucherReverseUrl,
//             type: "POST",
//             dataType: "json",
//             data: { vno: vno, csrf_test_name: csrf },
//             success: function(data)
//             {   
                
//                 if(data.success == "ok") {
//                     location.reload();
//                 }
//             },
//             // error: function (jqXHR, textStatus, errorThrown)
//             // {
//             //     alert('Error getting data from ajax');
//             // }
//         });
//     }
// });





// "use strict";

// $(document).on('click', '.v_reverse', function(e) {
//     e.preventDefault();
//     var vno = $(this).attr('data-id'); // Get voucher number from data attribute
//     var csrf = $('#csrfhashresarvation').val(); // Get CSRF token value
//     var voucherReverseUrl = window.appData.voucherReverseUrl; // Get URL from appData

//     // Show confirmation dialog
//     var conf = confirm('Are you sure you want to reverse this voucher?');

//     // If the user confirms, proceed with the AJAX request
//     if (conf) {
//         $.ajax({
//             url: voucherReverseUrl,  // Use the voucher reverse URL
//             type: "POST",            // Set the request type to POST
//             dataType: "json",        // Expect a JSON response
//             data: { 
//                 vno: vno,            // Send voucher number
//                 csrf_test_name: csrf // Send CSRF token
//             },
//             success: function(data) {
//                 if (data.success === "ok") {
//                     // location.reload();  // Reload the page if the operation is successful
//                 } else {
//                     alert("Error: Could not reverse voucher.");  // Show error if response is not success
//                 }
//             },
//             error: function(jqXHR, textStatus, errorThrown) {
//                 alert('Error getting data from AJAX. Please try again later.');
//             }
//         });
//     } else {
//         // If user cancels the confirmation, don't do anything
//         console.log("Voucher reversal cancelled.");
//     }
// });




"use strict";

$(document).on('click', '.v_reverse', function(e) {
    e.preventDefault();

    var vno = $(this).attr('data-id');  // Get voucher number from data attribute
    var csrf = $('#csrfhashresarvation').val();  // Get CSRF token
    var voucherReverseUrl = window.appData.voucherReverseUrl;  // Get the reverse URL

    // Log the URL to check if it's set properly
    console.log(voucherReverseUrl);  // This should print the correct URL

    // Use SweetAlert2 to confirm the action
    Swal.fire({
        title: 'Are you sure?',
        text: "You will not be able to recover this voucher!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reverse it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // If the user clicked 'Yes, reverse it!', perform the AJAX request
            $.ajax({
                url: voucherReverseUrl,  // Make sure this URL is correct
                type: "POST",
                dataType: "json",
                data: { 
                    vno: vno,  // Send voucher number
                    csrf_test_name: csrf  // Send CSRF token
                },
                success: function(data) {
                    if (data.success === "ok") {
                        Swal.fire(
                            'Reversed!',
                            'The voucher has been reversed successfully.',
                            'success'
                        ).then(() => {
                            location.reload();  // Reload the page to reflect the changes
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Could not reverse the voucher. Please try again.',
                            'error'
                        );
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong with the request. Please try again.',
                        'error'
                    );
                }
            });
        } else {
            // If the user clicked 'No, keep it', nothing happens
            console.log("Voucher reversal cancelled.");
        }
    });
});
$(document).ready(function() {

    // --- 1. Event handler to open the modal and populate data ---
    $('#ledger-body').on('click', '.schedule-status-btn', function() {
        const voucherId = $(this).data('id');
        const voucherNo = $(this).data('voucher-no');
        const voucherDate = $(this).data('voucher-date');
        const totalAmount = parseFloat($(this).data('total-amount')).toFixed(2);

        // Populate the modal fields
        $('#modalVoucherMasterId').val(voucherId);
        $('#modalTotalAmount').val(totalAmount);
        $('#modalTotalAmountText').text(totalAmount);
        $('#modalVoucherNo').text(voucherNo);
        $('#modalVoucherDate').text(voucherDate);

        // Clear previous entries
        $('#numberOfInstallments').val('');
        $('#deferredEffectiveDate').val('');
        $('#modalRemarks').val('');
        $('#installmentDetails').html('');

        // Show the modal
        $('#scheduleStatusModal').modal('show');
    });

    // --- 2. Function to calculate and display virtual installments ---
    function calculateInstallments() {
        const totalAmount = parseFloat($('#modalTotalAmount').val());
        const numberOfInstallments = parseInt($('#numberOfInstallments').val());
        const effectiveDateStr = $('#deferredEffectiveDate').val();

        $('#installmentDetails').html('');

        if (isNaN(totalAmount) || isNaN(numberOfInstallments) || numberOfInstallments <= 0 || !effectiveDateStr) {
            return; // Exit if data is not valid
        }

        const baseInstallmentAmount = parseFloat((totalAmount / numberOfInstallments).toFixed(2));
        const lastInstallmentAmount = (totalAmount - (baseInstallmentAmount * (numberOfInstallments - 1))).toFixed(2);

        let tableHtml = `
            <h5 class="mt-4">Installment Details</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th># Installment</th>
                        <th>Installment Amount</th>
                        <th>Installment Date</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Split the date string to avoid timezone issues when creating the Date object
        const dateParts = effectiveDateStr.split('-');
        const year = parseInt(dateParts[0]);
        const month = parseInt(dateParts[1]) - 1; // Month is 0-indexed in JS
        const day = parseInt(dateParts[2]);
        const effectiveDate = new Date(year, month, day);

        for (let i = 1; i <= numberOfInstallments; i++) {
            const currentInstallmentAmount = (i === numberOfInstallments) ? lastInstallmentAmount : baseInstallmentAmount;
            
            const installmentDate = new Date(effectiveDate);
            installmentDate.setMonth(installmentDate.getMonth() + (i - 1));
            
            // *** FIXED DATE FORMATTING LOGIC ***
            const instYear = installmentDate.getFullYear();
            const instMonth = ('0' + (installmentDate.getMonth() + 1)).slice(-2);
            const instDay = ('0' + installmentDate.getDate()).slice(-2);
            const formattedDate = `${instYear}-${instMonth}-${instDay}`;

            tableHtml += `
                <tr>
                    <td>${i}</td>
                    <td>${currentInstallmentAmount}</td>
                    <td>${formattedDate}</td>
                </tr>
            `;
        }

        tableHtml += `</tbody></table>`;
        $('#installmentDetails').html(tableHtml);
    }

    // Add event listeners to trigger the calculation
    $('#numberOfInstallments, #deferredEffectiveDate').on('input change', calculateInstallments);
    // Calculate the next month's first date
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    const formattedDate = $.datepicker.formatDate("yy-mm-dd", nextMonth);

    // Initialize the datepicker
    $(".effective_date_picker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        showAnim: "slideDown",
        minDate: nextMonth, // Disable current and previous months
    }).datepicker("setDate", formattedDate); // Set the default date
    // --- 3. Event handler for the Save button ---
    $('#saveDeferredSchedule').on('click', function() {
        if (!$('#numberOfInstallments').val() || !$('#deferredEffectiveDate').val()) {
            alert('Please fill in the number of installments and the effective date.');
            return;
        }

        const saveUrl =$('#save-deferred-schedule-url').val();
        const csrf = $('#csrfhashresarvation').val();
        const formData = {
            csrf_test_name: csrf,
            voucher_master_id: $('#modalVoucherMasterId').val(),
            expense_head: $('#expense_head').val(),
            number_of_installments: $('#numberOfInstallments').val(),
            effective_date: $('#deferredEffectiveDate').val(),
            remarks: $('#modalRemarks').val()
        };
        $('.page-loader-wrapper').show();
        $.ajax({
            url: saveUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
                $('.page-loader-wrapper').fadeOut();
                if (response.success) {
                    Swal.fire('Deferred Schedule Save',response.message,'success')
                    $('#scheduleStatusModal').modal('hide');
                    loadTransactions(); 
                } else {

                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                // This handles HTTP errors (like 422 for validation or 500 for server error)
                let errorMessage = 'An error occurred while saving the schedule.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                console.log(xhr.responseText); // Log the full error for debugging
                alert(errorMessage);
            }
        });
    });

});
$(function () {
    "use strict";

    // Using event delegation for dynamically created buttons
    $(document).on('click', '.remove-deferred-btn', function () {
        
        const voucherId = $(this).data('id');
        const csrf = $('#csrfhashresarvation').val(); // Ensure you have the correct CSRF token element
        const url = $('#remove-deferred-status-url').val(); 

        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove the deferred schedule!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: url,
                    data: {
                        _token: csrf, // Laravel's CSRF token
                        voucher_master_id: voucherId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Removed!',
                                response.message,
                                'success'
                            );
                            // Refresh the transaction list to reflect the change
                            loadTransactions(); 
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire(
                            'Failed!',
                            'An unexpected error occurred.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});


