<?php

function wooproddel_enqueue_select2() {
    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        wp_enqueue_style('wooproddel-select2-css', plugin_dir_url(__FILE__) . 'css/select2.min.css');
        wp_enqueue_script('wooproddel-select2-js', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), null, true);
        wp_enqueue_script('wooproddel-init-select2-js', plugin_dir_url(__FILE__) . 'js/init-select2.js', array('wooproddel-select2-js'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'wooproddel_enqueue_select2');



add_action('woocommerce_after_checkout_billing_form', 'wooproddel_checkout_timeslot_field', 20);
function wooproddel_checkout_timeslot_field($checkout) {
    $is_date_required = get_option('wooproddel_date_required', '');
    $required = $is_date_required === '1';

    // Get all timeslots without applying exclusion logic here
    $timeslots = get_option('wooproddel_timeslots', []);
    $timeslot_options = ['none' => __('Select a timeslot', 'wooproddel')];

    // Add all timeslots to the options array
    foreach ($timeslots as $timeslot) {
        $timeslot_value = sprintf('%s-%s', $timeslot['start'], $timeslot['end']);
        $timeslot_options[$timeslot_value] = $timeslot_value;
    }

    // Generate the timeslot selection field
    woocommerce_form_field('wooproddel_delivery_timeslot', array(
        'type'          => 'select',
        'class'         => array('form-row-wide', 'wooproddel-select2'),
        'label'         => __('Delivery Timeslot', 'wooproddel'),
        'required'      => $required,
        'options'       => $timeslot_options,
        'default'       => 'none',
    ), $checkout->get_value('wooproddel_delivery_timeslot'));
}




add_action('woocommerce_checkout_process', 'wooproddel_checkout_timeslot_field_process');
function wooproddel_checkout_timeslot_field_process() {
    if (empty($_POST['wooproddel_delivery_timeslot'])) {
        wc_add_notice(__('Please select a delivery timeslot.', 'wooproddel'), 'error');
    }
}

add_action('woocommerce_checkout_update_order_meta', 'wooproddel_checkout_timeslot_field_update_order_meta');
function wooproddel_checkout_timeslot_field_update_order_meta($order_id) {
    if (!empty($_POST['wooproddel_delivery_timeslot'])) {
        update_post_meta($order_id, 'wooproddel_delivery_timeslot', sanitize_text_field($_POST['wooproddel_delivery_timeslot']));
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'wooproddel_display_timeslot_admin_order_meta', 10);
function wooproddel_display_timeslot_admin_order_meta($order) {
    $timeslot = get_post_meta($order->get_id(), 'wooproddel_delivery_timeslot', true);
    if ($timeslot) {
        echo '<p><strong>' . __('Delivery Timeslot', 'wooproddel') . ':</strong> ' . esc_html($timeslot) . '</p>';
    }
}
//////
function wooproddel_process_timeslot_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['wooproddel_timeslot_nonce']) && wp_verify_nonce($_POST['wooproddel_timeslot_nonce'], 'wooproddel_save_timeslot')) {
        $timeslots = get_option('wooproddel_timeslots', []);
        
        // Processing adding of new timeslot
        if (isset($_POST['wooproddel_timeslot_action']) && $_POST['wooproddel_timeslot_action'] === 'add') {
            $start_time = sanitize_text_field($_POST['wooproddel_timeslot_start']);
            $end_time = sanitize_text_field($_POST['wooproddel_timeslot_end']);

            if (wooproddel_validate_time_format($start_time) && wooproddel_validate_time_format($end_time)) {
                if (!wooproddel_check_timeslot_overlap($timeslots, $start_time, $end_time)) {
                    $timeslots[] = array('start' => $start_time, 'end' => $end_time);
                    update_option('wooproddel_timeslots', $timeslots);
                    add_settings_error('wooproddel_timeslot_messages', 'wooproddel_timeslot_added', __('New timeslot added.', 'customize-product-delivery-date'), 'updated');
                } else {
                    add_settings_error('wooproddel_timeslot_messages', 'wooproddel_timeslot_overlap', __('The timeslot overlaps with an existing timeslot.', 'customize-product-delivery-date'), 'error');
                }
            } else {
                add_settings_error('wooproddel_timeslot_messages', 'wooproddel_invalid_time', __('Please enter a valid time in 24-hour format. E.g., 14:00', 'customize-product-delivery-date'), 'error');
            }
        }

        // Processing deletion of a timeslot
        if (isset($_POST['wooproddel_timeslot_action']) && $_POST['wooproddel_timeslot_action'] === 'delete' && isset($_POST['timeslot_id'])) {
            $timeslot_id = intval($_POST['timeslot_id']);
            if (isset($timeslots[$timeslot_id])) {
                unset($timeslots[$timeslot_id]);
                update_option('wooproddel_timeslots', $timeslots);
                add_settings_error('wooproddel_timeslot_messages', 'wooproddel_timeslot_deleted', __('Timeslot deleted.', 'customize-product-delivery-date'), 'updated');
            }
        }
    }
}

function wooproddel_validate_time_format($time) {
    return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time);
}

function wooproddel_check_timeslot_overlap($timeslots, $start_time, $end_time) {
    foreach ($timeslots as $slot) {
        if (($start_time >= $slot['start'] && $start_time < $slot['end']) || ($end_time > $slot['start'] && $end_time <= $slot['end'])) {
            return true;
        }
    }
    return false;
}

function wooproddel_timeslot_settings_page_callback() {
    wooproddel_process_timeslot_actions();
    settings_errors('wooproddel_timeslot_messages');

    $timeslots = get_option('wooproddel_timeslots', []);

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('wooproddel_save_timeslot', 'wooproddel_timeslot_nonce'); ?>
            <h2><?php _e('Add New Timeslot', 'customize-product-delivery-date'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="wooproddel_timeslot_start"><?php _e('Start Time', 'customize-product-delivery-date'); ?></label></th>
                        <td><input name="wooproddel_timeslot_start" type="time" id="wooproddel_timeslot_start" value="" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="wooproddel_timeslot_end"><?php _e('End Time', 'customize-product-delivery-date'); ?></label></th>
                        <td><input name="wooproddel_timeslot_end" type="time" id="wooproddel_timeslot_end" value="" class="regular-text"></td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="wooproddel_timeslot_action" value="add">
            <?php submit_button(__('Add Timeslot', 'customize-product-delivery-date')); ?>
        </form>
        <h2><?php _e('Existing Timeslots', 'customize-product-delivery-date'); ?></h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Start Time', 'customize-product-delivery-date'); ?></th>
                    <th><?php _e('End Time', 'customize-product-delivery-date'); ?></th>
                    <th><?php _e('Action', 'customize-product-delivery-date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeslots as $id => $timeslot): ?>
                    <tr>
                        <td><?php echo esc_html($timeslot['start']); ?></td>
                        <td><?php echo esc_html($timeslot['end']); ?></td>
                        <td>
                            <form method="post" action="">
                                <?php wp_nonce_field('wooproddel_save_timeslot', 'wooproddel_timeslot_nonce'); ?>
                                <input type="hidden" name="wooproddel_timeslot_action" value="delete">
                                <input type="hidden" name="timeslot_id" value="<?php echo esc_attr($id); ?>">
                                <?php submit_button(__('Delete', 'customize-product-delivery-date'), 'delete small', 'submit', false); ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($timeslots)): ?>
                    <tr>
                        <td colspan="3"><?php _e('No timeslots have been added yet.', 'customize-product-delivery-date'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}


