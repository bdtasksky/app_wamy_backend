$("#subtype_id").change(function () {
    "use strict";
    var subtypeid = $("#subtype_id").val();
    var getCoaUrl = window.appData.getCoaUrl.replace(":id", subtypeid);

    $.ajax({
        type: "GET",
        url: getCoaUrl, // Use the correct URL here
        async: false,
        success: function (response) {
            // Clear previous options
            $("#acc_coa_id").html("");
            $("#acc_subcode_id").html("");

            // Handle COA Dropdown
            var coaOptions = "<option value=''>None</option>"; // Default option for COA
            $.each(response.coaDropDown, function (key, value) {
                coaOptions +=
                    "<option value='" +
                    value.id +
                    "'>" +
                    value.account_name +
                    "</option>";
            });
            $("#acc_coa_id").append(coaOptions);

            // Handle Subcode Dropdown
            var subcodeOptions = "<option value='all'>All</option>"; // Default option for Subcode
            $.each(response.subcode, function (key, value) {
                subcodeOptions +=
                    "<option value='" +
                    value.id +
                    "'>" +
                    value.name +
                    "</option>";
            });
            $("#acc_subcode_id").append(subcodeOptions);
        },
    });
});

("use strict");
function printDiv() {
    var divName = "printArea";
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
}
function viewvouchar(vid, vtype) {
    var csrf = $('meta[name="csrf-token"]').attr("content");

    var getVoucherDetails = window.appData.getVoucherDetails;

    $.ajax({
        type: "POST",
        url: getVoucherDetails,
        dataType: "JSON",
        data: { vid: vid, vtype: vtype, _token: csrf },
        success: function (res) {
            //alert(res)
            $("#all_vaucher_view").html(res.data);
            //   $("a#pdfDownload").prop("href", basicinfo.baseurl+res.pdf);
            //document.getElementById("pdfDownload").setAttribute("href", base_url+"/"+ res.pdf);
            $("#allvaucherModal").modal("show");
        },
    });
}
function printVaucher(modald) {
    var divName = "vaucherPrintArea";
    var printContents = document.getElementById(modald).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    setTimeout(function () {
        location.reload();
    }, 100);
}
