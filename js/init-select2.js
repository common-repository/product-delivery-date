jQuery(document).ready(function($) {
    function initializeSelect2() {
        if ($.fn.select2 && $('#wooproddel_delivery_timeslot').length) {
            $('#wooproddel_delivery_timeslot').select2();
        }
    }

    initializeSelect2();

    $(document.body).on('updated_checkout', initializeSelect2);
});
