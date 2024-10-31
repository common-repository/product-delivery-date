<?php
function wooproddel_enqueue_custom_js() {
    if (function_exists('is_checkout') && is_checkout() && get_option('wooproddel_exclude_timeslots', '0') === '1') {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wooproddel-custom-js', plugin_dir_url(__FILE__) . '../js/wooproddel-exclude-timeslots.js', array('jquery'), '1.0', true);

        $timeslots = get_option('wooproddel_timeslots', []);
        if (is_string($timeslots)) {
            $timeslots = json_decode($timeslots, true) ?: [];
        }

        // Fetch WordPress timezone offset in hours.
        $gmt_offset = get_option('gmt_offset');
        $timezone_offset_seconds = $gmt_offset * HOUR_IN_SECONDS;

        wp_localize_script('wooproddel-custom-js', 'wooproddelParams', array(
            'currentTimestamp' => current_time('timestamp', 1), // GMT timestamp
            'timezoneOffset' => $timezone_offset_seconds,
            'timeslots' => $timeslots,
            'excludeTimeslots' => get_option('wooproddel_exclude_timeslots', 0),
        ));
    }
}
add_action('wp_enqueue_scripts', 'wooproddel_enqueue_custom_js');





?>
