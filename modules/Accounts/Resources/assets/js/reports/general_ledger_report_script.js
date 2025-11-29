	"use strict";
function printDiv() {
    var divName = "printArea";
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
}
function viewvouchar(vid,vtype){
	    var csrf = $('meta[name="csrf-token"]').attr('content');
	    var submit_url =basicinfo.baseurl+'accounts/accounts/voucherDetails';
		$.ajax({
                type: 'POST',
                url: submit_url,
                dataType : 'JSON',
                data: {vid:vid, vtype:vtype,_token:csrf},
                success: function(res) {
                    // alert(res)                  
                  $('#all_vaucher_view').html(res.data);
                  $("a#pdfDownload").prop("href", basicinfo.baseurl+res.pdf);     
                  //document.getElementById("pdfDownload").setAttribute("href", base_url+"/"+ res.pdf);             
                  $('#allvaucherModal').modal('show');
                }
            });  
	}
function printVaucher(modald) {
    var divName = "vaucherPrintArea";
    var printContents = document.getElementById(modald).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
	setTimeout(function() {
            location.reload();
           }, 100);
    
}