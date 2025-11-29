$(document).on('click', '#uploadOb', function () {
    $('#leadForm').submit();
});


$(document).ready(function() {
    $('#filter').click(function() {
        var financial_year_id = $('#financial_year_id').val();
        var table = $('#opening-balance-table');
        table.on('preXhr.dt', function(e, settings, data) {
            data.financial_year_id = financial_year_id;
        });
        table.DataTable().ajax.reload();
    });

    $('#reset').click(function() {
        $('#financial_year_id').val($('#financial_year_id option:first').val()).trigger('change');
        var table = $('#opening-balance-table');
        table.on('preXhr.dt', function(e, settings, data) {
            data.financial_year_id = '';
        });
        table.DataTable().ajax.reload();
    });
})

