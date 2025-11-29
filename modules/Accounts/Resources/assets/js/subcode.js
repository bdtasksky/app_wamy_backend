$(document).ready(function(){
    $(document).on('click','.edit-SubCode',function(){
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: "GET",
            success: function(response){
                $('#editSubCodeData').html(response);
                $('#edit-SubCode').modal('show');
            }
        });
    });

    "use strict";
    $(document).on('click', '.delete-subcode', function () {
        let url = $(this).data('route'); // Ensure data-route has correct DELETE route
        let csrf = $(this).data('csrf');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this subcode?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',  // Use DELETE instead of POST
                    dataType: 'json',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': csrf  // CSRF token in headers
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'Subcode has been deleted successfully.',
                                'success'
                            );
                            location.reload();  // Refresh page or remove row dynamically
                        } else {
                            Swal.fire(
                                'Error!',
                                response.error,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.error || 'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
$(document).on('change','#acc_subtype_id',function(){
    var url = $('#get_refer_url').val();
    var subtype = $('#acc_subtype_id').val();
    var csrf_token = $('input[name="_token"]').val();
    $.ajax({
        url: url,
        data: {
            subtype:subtype,
            _token: csrf_token
        },
        type: "POST",
        success: function(data){
            var $select2 = $('#load_reference_no');
            $select2.empty();
            $select2.append('<option value="">Select Reference No.</option>');
            data.forEach(function(item) {
                $select2.append('<option value="' + item.id + '">' + item.name + '</option>');
            });
        }
    });
});
