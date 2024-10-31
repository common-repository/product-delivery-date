jQuery(document).ready(function ($) {
    $('#wooproddel_add_excluded_date').on('click', function () {
        var newDate = $('#wooproddel_new_excluded_date').val();
        $.post(wooproddelExcludedDates.ajaxurl, {
            action: 'wooproddel_add_excluded_date',
            date: newDate,
            security: wooproddelExcludedDates.nonce
        }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message);
            }
        });
    });

    // The on click event is now delegated from the document to dynamically handle elements created after the DOM is loaded.
    $(document).on('click', '.wooproddel_remove_date', function () {
        // The date to remove is now being captured using the closest 'li' and finding the text within that.
        var dateItem = $(this).closest('li');
        var dateToRemove = dateItem.find('span.date').text(); // Ensure there is a <span> with class 'date' in the HTML that contains the date text.
        if (dateToRemove) {
            $.post(wooproddelExcludedDates.ajaxurl, {
                action: 'wooproddel_remove_excluded_date',
                date: dateToRemove,
                security: wooproddelExcludedDates.nonce
            }, function (response) {
                if (response.success) {
                    dateItem.remove(); // If successful, remove the date item from the list without refreshing.
                } else {
                    alert(response.data.message);
                }
            });
        } else {
            alert('Error: Date to remove not found.');
        }
    });
    // Get disabled dates from the localized script
    var disabledDates = wooproddel_params.excludedDates || [];

    // Disable excluded dates in the datepicker
    $('#wooproddel_delivery_date').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: wooproddel_params.minDate,
        maxDate: wooproddel_params.maxDate,
        beforeShowDay: function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
            return [disabledDates.indexOf(string) === -1];
        }
    });
});
