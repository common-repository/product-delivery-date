jQuery(document).ready(function($) {
    // Hide the timeslot dropdown initially
    var $timeslotSelect = $('#wooproddel_delivery_timeslot');
    var $timeslotContainer = $timeslotSelect.closest('.form-row-wide');
    $timeslotContainer.hide();

    $('#wooproddel_delivery_date').change(function() {
        var selectedDate = $(this).val();

        // Show the timeslot dropdown only if a date is selected
        if (selectedDate) {
            $timeslotContainer.show();

            var excludeTimeslots = wooproddelParams.excludeTimeslots === '1';
            var timezoneOffset = parseInt(wooproddelParams.timezoneOffset);
            var currentTimestamp = new Date().getTime() + timezoneOffset;

            // Clear timeslots initially
            $timeslotSelect.find('option:gt(0)').remove(); // Remove all but the first option

            // Append new timeslots based on the logic
            $.each(wooproddelParams.timeslots, function(index, timeslot) {
                var slotTime = new Date(selectedDate + ' ' + timeslot.start).getTime();
                // Adjust the slotTime with timezone offset
                slotTime += timezoneOffset;

                // Check if the timeslot is at least 24 hours from the current time
                if (excludeTimeslots && (slotTime - currentTimestamp) < 86400000) {
                    // If within 24 hours, don't append this option
                    return true; // continue to the next iteration
                }

                // Append the option as it's valid
                $timeslotSelect.append($('<option></option>').attr('value', timeslot.start + '-' + timeslot.end).text(timeslot.start + ' - ' + timeslot.end));
            });

            // Reinitialize the dropdown with theme
            if ($.fn.select2) {
                $timeslotSelect.select2({
                    theme: "default" // Adjust the theme to match your site's style
                });
            }
        } else {
            // Hide the timeslot dropdown if no date is selected
            $timeslotContainer.hide();
        }
    });
});
