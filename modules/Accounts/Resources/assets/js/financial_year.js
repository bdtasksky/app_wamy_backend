function year() {
    "use strict";
    var start = $("#start_date").val();
    var end = $("#end_date").val();
    var start_year = start.split("-");
    var end_year = end.split("-");
    if (start_year[0] <= end_year[0]) {
        $("#title").val(start_year[0]);
    } else {
        swal({
            title: "Failed",
            text: "End year can not greater than start year",
            type: "error",
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ok",
            closeOnConfirm: true
        });
        $("#start_date").val(end);
        $("#start_date,#end_date").trigger("change");
    }
}
$("#start_date,#end_date").trigger("change");
function nextyear() {
    "use strict";
    var start = $("#next_year_from_date").val();
    var end = $("#next_year_to_date").val();
    var replace_end = $("#temp_next_year_to_date").val();
    
    var start_year = start.split("-");
    var end_year = end.split("-");
    if (start_year[0] <= end_year[0]) {
        $("#next_year_title").val(start_year[0]);
    } else {
        swal({
            title: "Failed",
            text: "End year can not greater than start year",
            type: "error",
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ok",
            closeOnConfirm: true
        });
        $("#next_year_to_date").val(replace_end).trigger("change");
    }
}
function editfinyear(id){
    "use strict";
    var title = $('#title_'+id).text();
    var start = $('#start_'+id).text();
    var end = $('#end_'+id).text();
    var status = $('#status_'+id).text();
    if(status=="Active"){
        $("input[name=status][value='2']").prop("checked",true);
        $("input[name=status][value='0']").prop("checked",false);
    }else{
        $("input[name=status][value='2']").prop("checked",false);
        $("input[name=status][value='0']").prop("checked",true);
    }
    $("#finid").val(id);
    $("#title").val(title);
    $("#start_date").val(start);
    $("#end_date").val(end);
    $("#start_date,#end_date").trigger("change");
    $("#finsubmit").attr('hidden', false);
    $("#submit").attr("hidden", true);
    $("#finsubmit").attr("onclick", "updatefinyearonly()");
}
function updatefinyearonly(){
    "use strict";
    var finYearUpdateUrl = window.appData.finYearUpdateUrl; 
    var id = $("#finid").val();
    var title = $("#title").val();
    var start = $("#start_date").val();
    var end = $("#end_date").val();
	var csrf = $('#csrfhashresarvation').val();
    var status = $("input[name=status]:checked").val();
    $.ajax({
        type: "POST",
        url: finYearUpdateUrl,
        data: {
            csrf_test_name: csrf,
            id: id,
            title: title,
            start: start,
            end: end,
            status: status,
        },

        success: function(data) {
            location.reload();
        }
    });
}

function submitYearEnd(fiyear_id){
    "use strict";
    var next_year_title = $("#next_year_title").val();
    var next_year_from_date = $("#next_year_from_date").val();
    var next_year_to_date = $("#next_year_to_date").val();
    var old_year_form_date = $("#old_year_form_date").val();
    var old_year_to_date = $("#old_year_to_date").val();
    var csrf = $('#csrfhashresarvation').val();
    
    $.ajax({
        type: "POST",
        url: window.appData.finYearEndUrl,
        data: {
            csrf_test_name: csrf,
            fiyear_id: fiyear_id,
            old_year_form_date: old_year_form_date,
            old_year_to_date: old_year_to_date,
            next_year_title: next_year_title,
            next_year_from_date: next_year_from_date,
            next_year_to_date: next_year_to_date,
        },
        success: function(response) {
            console.log(response);
            if (response.status === 'success') {
                Swal.fire(
                    'Warning!',
                    response.message,
                    'warning'
                )
            } else {
                Swal.fire(
                    'Error!',
                    response.message,
                    'error'
                ).then(() => {
                    location.reload();
                });
            }
        },
        error: function() {
            Swal.fire(
                'Error!',
                response.message,
                'An error occurred while processing the year-end request.'
            ).then(() => {
                location.reload();
            });
        }
    });
}


"use strict";
$(document).on('click', '.financial_year_modal', function(e) {
    e.preventDefault();  // Prevent the default link action

    var openBookUrl = window.appData.openBookUrl;  // Get the route URL   // Get the CSRF token
    var year_id = $(this).data('year_id');  // Get Financial Year ID
    var year_title = $(this).data('year_title');
    var modal_type = $(this).data('modal_type');
    var csrf = $('#csrfhashresarvation').val();  // CSRF token
        $.ajax({
            type: 'POST',
            url: openBookUrl,
            dataType: 'JSON',
            data: { year_id: year_id,modal_type: modal_type, csrf_test_name: csrf },
            success: function(res) {
                $('#financial_year_view').html(res.data);
                $('#financialyearModal').modal('show');
                if(modal_type=='2'){
                    $('#ModalLabel').text('Year Ending for ' + year_title);
                }else{
                    $('#ModalLabel').text('Update Financial Year ' + year_title);
                    editfinyear(year_id);
                }
                $(".datepicker5").datepicker({
                    dateFormat: "yy-mm-dd"
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error occurred while fetching Financial Year data.');
            }
        });
});


function updateStepIndicator(step) {
for (let i = 1; i <= 6; i++) {
    const indicator = document.getElementById("stepIndicator" + i);
    if (i < step) {
    indicator.classList.add("completed");
    indicator.classList.remove("active");
    } else if (i === step) {
    indicator.classList.add("active");
    indicator.classList.remove("completed");
    } else {
    indicator.classList.remove("active", "completed");
    }
}
}

function nextStep(currentStep) {
const checkbox = document.querySelector("#step" + currentStep + " .step-checkbox");
if (checkbox.checked) {
    document.getElementById("step" + currentStep).classList.remove("active");
    document.getElementById("step" + (currentStep + 1)).classList.add("active");
    updateStepIndicator(currentStep + 1);
} else {
    alert("Please check the box before proceeding.");
}
}

function prevStep(currentStep) {
document.getElementById("step" + currentStep).classList.remove("active");
document.getElementById("step" + (currentStep - 1)).classList.add("active");
updateStepIndicator(currentStep - 1);
}

// document.getElementById("formWizard").onsubmit = function (event) {
// event.preventDefault();
// alert("Form submitted successfully!");
// };
