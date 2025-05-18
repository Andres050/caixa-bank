import $ from "jquery";

$(document).ready(function() {
    // Initialize Select2
    console.log('Select2 is working!');
    $('.select2').each(function (index, select2) {
        $(select2).select2({
            placeholder: select2.getAttribute('data-default'),
            allowClear: true
        });
    });
});
