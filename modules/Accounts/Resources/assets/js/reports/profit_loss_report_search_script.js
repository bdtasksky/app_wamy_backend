$(document).ready(function () {
    // Function to check and hide rows
    function checkAndHideRows() {
        $('#profit-loss-report tbody tr').each(function () {
            var columnsToCheck = [1, 2, 3, 4]; // Adjust to your column indices
            var hasNonZeroValue = false;

            for (var i = 0; i < columnsToCheck.length; i++) {
                var value = parseFloat($(this).find('td:eq(' + columnsToCheck[i] + ')').text());
                if (value > 0.00) {
                    hasNonZeroValue = true; // Found a non-zero value
                    break; // No need to check other columns if one is non-zero
                }
            }

            // Hide the row if there are no non-zero values
            if (!hasNonZeroValue) {
                $(this).hide();
            } else {
                $(this).show(); // Show the row if there's a non-zero value
            }
        });
    }
    // Toggle the visibility of rows when the button is clicked
    let isFiltered = false; // Track the filter state
    $('#hideUnhideButton').on('click', function () {
        if (isFiltered) {
            $('#profit-loss-report tbody tr').show(); // Show all rows
        } else {
            checkAndHideRows(); // Hide rows with only zeros
        }
        isFiltered = !isFiltered; // Toggle the filter state
    });
});
"use strict";
function printDiv() {
    var divName = "printArea";
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
// function getchildhead(headid,fromdate,todate,headname){
// 	    var csrf = $('#csrfhashresarvation').val();
// 	    var myurl =basicinfo.baseurl+'accounts/accounts/profitlossparent';
// 	    var dataString = "headid="+headid+'&headname='+headname+'&fromdate='+fromdate+'&todate='+todate+'&csrf_test_name='+csrf;
// 		 $.ajax({
// 		 type: "POST",
// 		 url: myurl,
// 		 data: dataString,
// 		 success: function(data) {
// 			 $('#all_transaction').html(data);
// 			 $('#showparenthead').modal('show');
// 		 } 
// 	});
// 	}
// function getgledger(headid,headname,fromdate,todate,phead,pheadname){
// 		$("#showparenthead").modal('hide');
// 	    var csrf = $('#csrfhashresarvation').val();
// 	    var myurl =basicinfo.baseurl+'accounts/accounts/generallegder_by_head';
// 	    var dataString = "headid="+headid+'&headname='+headname+'&fromdate='+fromdate+'&todate='+todate+'&csrf_test_name='+csrf;
// 		 $.ajax({
// 		 type: "POST",
// 		 url: myurl,
// 		 data: dataString,
// 		 success: function(data) {
// 			 $('#closenext').attr('onclick', 'closeledger('+phead+',"'+fromdate+'","'+todate+'","'+pheadname+'")');
// 			 $('#pshowledger').html(data);
// 			 $('#showledger').modal({
//                   backdrop: 'static',
//                   keyboard: false
//               });
// 			 //$('#showledger').modal('show');
// 		 } 
// 	});
// 	}
// function closeledger(headid,fromdate,todate,headname){
// 		$('#closenext').removeAttr("onclick");
// 		$('#showledger').modal('hide');
// 		getchildhead(headid,fromdate,todate,headname);
// 	}
	
	