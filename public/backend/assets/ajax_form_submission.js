
// Globally form submit ajax call
(function() {
    'use strict';
     window.addEventListener('load', function() {
     // Fetch all the forms we want to apply custom Bootstrap validation styles to
     var forms = document.getElementsByClassName('needs-validation');
     var html = '<span id="generate_loading" class="d-none"><i class="fa fa-spinner fa-spin"></i>Please Wait...</span>';
     // Loop over them and prevent submission
     var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                toastr.warning('Please fulfill all required fields.', 'Required Missing!');
               
                $("#generate_submit").removeClass("d-none");
                $("#generate_loading").addClass("d-none");

                $('.actionBtn').prop('disabled', false);
                // $(".actionBtn").attr("data-kt-indicator", '');
             }else {
                $("#generate_submit").addClass("d-none");
                $("#generate_loading").removeClass("d-none");
                $('.actionBtn').prop('disabled', true);
                
                ajaxSubmit(event, $(this));
             }
             form.classList.add('was-validated');
            //  $('.form-control.is-valid, .was-validated .form-control:valid').css({'background-image' : 'none','border-color' : '#dee2e6'});
         }, false);
     });
   }, false);
})();



//Form Submition
function ajaxSubmit(e, form) {
    e.preventDefault();

    var action = form.attr('action');
    var CallBackFunction = eval(form.attr('data'));
    var form2 = e.target;
    var data = new FormData(form2);
    $.ajax({
        type: "POST",
        url: action,
        processData: false,
        contentType: false,
        dataType: 'json',
        data: data,
        success: function(response)
        {
            // console.log(response);
            $('.actionBtn').prop('disabled', false);
            // $(".actionBtn").attr("data-kt-indicator", '');
            $("#generate_submit").removeClass("d-none");
            $("#generate_loading").addClass("d-none");
            if(response.success==true) {
                toastr.success(response.message, response.title);
                if (typeof CallBackFunction == 'function') {
                    if(response.hasOwnProperty('data')){
                        CallBackFunction(response.data);
                    }else{
                        CallBackFunction();
                    }
                }
            }else if(response.success=='exist'){
                toastr.warning(response.message, response.title);
            }else{
                toastr.error(response.message, response.title);
            }
        }
    });
}


// $('.acBtn').on('click', function() {
//     $("form").submit(function(){
//         $(".generatesubmit").addClass("d-none");
//         $(".generateloading").removeClass("d-none");
//         $(".acBtn").prop('disabled', true);
//     });

//     $(".generatesubmit").removeClass("d-none");
//     $(".generateloading").addClass("d-none");
//     $(".acBtn").prop('disabled', false);

// });

// $('.acBtn').on('click', function() {
//     $(".generatesubmit").addClass("d-none");
//     $(".generateloading").prop('disabled', true);
//     $(".generateloading").removeClass("d-none");
// });











