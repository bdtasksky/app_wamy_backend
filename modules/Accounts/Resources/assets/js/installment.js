


$(document).ready(function(e) {
    "use strict";
    

    function installmentGrandCalculation() {
        var installment_amount = Number($('#installment-month-amount').val());
        var installment_period = Number($('#installment-period').val());
        if(installment_period > 50){
            alert('Installment period should not be more than 50');
            $('#installment-period').val('');
            return false;
        }
        var per_installment = Math.round((installment_amount) / installment_period);
    
        // Update installment amount
        $('#installment-amount').val(per_installment);
    
        // Generate a table with installment details
        var installment_table = `
            <hr> 
            <div>
                <h4 class="text-center">Installment Details</h4>
            </div>
            <table class="table table-sm table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Number Of Installment</th>
                        <th>Installment Amount</th>
                        <th>Installment Date</th>
                    </tr>
                </thead>
                <tbody>
        `;
    
        // Get the effective date and parse it as a Date object
        var effective_date = $('#effective_date').val();
        var installment_date = effective_date ? new Date(effective_date) : new Date();
    
      
        for (var i = 0; i < installment_period; ++i) {
            
            if(i == 0){
                installment_date.setMonth(installment_date.getMonth());
    
            }else {
                installment_date.setMonth(installment_date.getMonth() + 1);
            }
            installment_table += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${per_installment}</td>
                    <td>
                        ${installment_date.toLocaleDateString('en-CA')}
                        <input type="hidden" name="installment_date[]" value="${installment_date.toLocaleDateString('en-CA')}">
                    </td>
                </tr>
            `;
        }
    
        installment_table += `
                </tbody>
            </table>
        `;
    
        $('#installment-details').html(installment_table);
    }
 
    $('#installment-month-amount, #effective_date, #installment-amount, #installment-period')
    .keyup(installmentGrandCalculation)
    .change(installmentGrandCalculation);
    

    


    // Calculate the next month's first date
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    const formattedDate = $.datepicker.formatDate("yy-mm-dd", nextMonth);

    // Initialize the datepicker
    $(".installment_date_picker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        showAnim: "slideDown",
        minDate: nextMonth, // Disable current and previous months
    }).datepicker("setDate", formattedDate); // Set the default date
});

"user strict";
function approveView(id) {

    var url = $('#approveView-' + id).data('url');
    var csrf = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            id: id,
            _token: csrf,
        },
        success: function (data) {
            if (data) {
                $('#viewData').html('');
                $('#viewData').html(data);
                $('#approveDetailsViewModal').modal('show');
            }
        },
        error: function (data) {
            toastr.error('Error', 'Error');
        }
    });
}
//Custom Datatable Search
$(document).ready(function() {

    $('#installment-filter').click(function() {
        var employee_id = $('#employee_name').val();        
        
        var table = $('#employee-installment-table');
        table.on('preXhr.dt', function(e, settings, data) {
            data.employee_id = employee_id;
        });
        table.DataTable().ajax.reload();
    });

    $('#installment-search-reset').click(function() {

        $('#employee_name').val(0).trigger('change');

        var table = $('#employee-installment-table');
        table.on('preXhr.dt', function(e, settings, data) {

            $("#employee_name").select2({
                placeholder: "Select Employee"
            });
            data.employee_id = '';
        });
        table.DataTable().ajax.reload();
    });
})

