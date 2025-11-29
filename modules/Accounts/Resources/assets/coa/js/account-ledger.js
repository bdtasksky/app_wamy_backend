"use strict";
function calculationDebtOpen(sl) {
    var gr_tot = 0;
    $(".total_dprice").each(function() {
        isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
    });
    $("#grandTotald").val(gr_tot.toFixed(2,2));
    checkEquel();

    
}
"use strict";
function calculationCreditOpen(sl) {

    var gr_tot = 0;
    $(".total_cprice").each(function() {
        isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
    });

    $("#grandTotalc").val(gr_tot.toFixed(2,2));
    checkEquel()
}

"use strict";
function checkEquel() {
    var voucher_type=$("input[name=voucher_type]").val();
    var ob =$("#opening_balance_form").val();
    if(voucher_type=='Journal' || ob==1){
        $('#create_submit').prop('disabled',true);
        var grandTotald = parseFloat($('#grandTotald').val());
        var grandTotalc = parseFloat($('#grandTotalc').val());

        if(grandTotald == grandTotalc){
            $('#create_submit').prop('disabled',false);
        }else{
            $('#create_submit').prop('disabled',true);

        }
    }

}



"use strict";
function get_subtypeCode(id,sl){
    var baseurl = $("#base_url").val();
    $.ajax({
        url :  baseurl + "/accounts/getsubtypbyid/" + id,
        type: "GET",
        dataType: "json",
        success: function(data) {
            if(data.subType != 1) {
                $('#isSubtype_'+sl).val(data.subType);
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

"use strict";
function load_subtypeOpen(id,sl){
    var baseurl = $("#base_url").val();
    get_subtypeCode(id,sl);
    $.ajax({
        url : baseurl + "/accounts/getsubtypecode/" + id,
        type: "GET",
        dataType: "json",
        success: function(data) {
            if(data != '') {
                $('#subtype_'+sl).html(data);
                $('#subtype_'+sl).removeAttr("disabled");
            } else {
                $('#subtype_'+sl).attr("disabled","disabled");
                $('#subtype_'+sl).find('option').remove();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
}

$(document).on('change', '#acc_coa_id', function() {
    "use strict";
    var is_banknature = $(this).find(':selected').data('isbanknature');
    if(is_banknature == 1) {
        $('#bank_nature_field').removeClass('d-none');
    } else {
        $('#bank_nature_field').addClass('d-none');
        $("#bank_nature_field input").val('');
    }
});

$(document).ready(function () {

   
    // Function to update date input with the last date of the selected financial year
    function getLastDateOfYear(year) {
        console.log(year, 'year');
        // Create a new Date object for January 1st of the next year
        year = year.replace(/ /g, '');
        var nextYear = new Date(parseInt(year) + 1, 0, 1);

        // Subtract one day to get the last day of the selected year
        var lastDate = new Date(nextYear - 1);

        // Format the date to "YYYY-MM-DD"
        var formattedLastDate = lastDate.toISOString().split('T')[0];

        console.log(formattedLastDate, 'formattedLastDate');

        return formattedLastDate;
    }

    // Attach change event listener to the financial_year_id select input
    $('#financial_year_id').on('change', function () {
       
        var selectedFinancialYearText = $(this).find(":selected").text();
        var thisYearLastDate = getLastDateOfYear(selectedFinancialYearText);
        console.log(selectedFinancialYearText);

        $('#date').val(thisYearLastDate);
    });

    checkEquel()

    


   
    
    $('#addmore').on('click', function() {
        var fileInput = `
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="file" class="form-control" name="attachment[]">
                        <input type="text" placeholder="Attachment Name" class="form-control" name="attachment_name[]"><br>
                        <div class="input-group-append">
                            <span class="input-group-text btn btn-danger-soft btn-sm remove-file">
                                <i class="fa fa-trash"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#fileInputs').append(fileInput);
    });

    $(document).on('click', '.remove-file', function() {
        $(this).closest('.row').remove();
    });



    $(document).ready(function() {

        $('.deleteAttachment').on('click', function() {

            var submit_url = $(this).attr('data-delete-route');

            // Show SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET', // Or 'DELETE' if appropriate
                        url: submit_url,
                        data: {"_token": "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == true) {
                                $("#fileInputs").load(" #fileInputs > *");
                                Swal.fire('Deleted!', response.message, 'success');
                            } else if (response.success == 'exist') {
                                Swal.fire('Warning!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        });


    });

});
