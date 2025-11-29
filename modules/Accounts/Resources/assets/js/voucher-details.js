"use strict";
$(document).on('click', '.v_view', function(e) {
    e.preventDefault();  // Prevent the default link action

    var vid = $(this).data('id');  // Get voucher ID
    var vdate = $(this).data('vdate');  // Get voucher date
    var csrf = $('meta[name="csrf-token"]').attr('content');  // CSRF token
    var voucherDetailsUrl = window.appData.voucherDetailsUrl; 

    // Perform AJAX request directly in this method
    $.ajax({
        type: 'POST',
        url: voucherDetailsUrl,
        data: { 
            vid: vid,
            vdate: vdate,
            _token: csrf
        },
        success: function(res) {
            let data = res.data;
                $('#all_vaucher_view').html(data);
                // Set the PDF download link
                // $("a#pdfDownload").prop("href", basicinfo.baseurl + data.pdf);
                // $(".rmpdf").attr("onclick", "removePDF('" + data.pdf+ "')");
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
"use strict";
function removePDF(link) {
    var filePath=link;
    var csrf = $('#csrfhashresarvation').val();  // CSRF token
    var pdfDeleteUrl = window.appData.pdfDeleteUrl; 

    $.ajax({
        url: pdfDeleteUrl,
        type: 'POST',
        data: { file_path: filePath, csrf_test_name: csrf },
        success: function(response) {
            //var result = JSON.parse(response);
            // if (result.status === 'success') {
            //     alert(result.message);
            // } else {
            //     alert(result.message);
            // }
        },
        error: function(xhr, status, error) {
            console.error('Error: ' + error);
        }
    });
}