<?PHP
add_shortcode("wooproddel_calendar", "wooproddel_calendar_shortcode_update");
add_shortcode("wooproddel_calendar_button", "wooproddel_calendar_shortcode_update_button");
// Register Ajax action
add_action( 'wp_ajax_update_delivery_date', 'update_delivery_date_handler' );
include_once plugin_dir_path( __FILE__ ) . 'helper/tableexport.php';

// Register the script
function wooproddel_calendar_scripts() {
    // Assume you have a 'js' folder in your plugin's directory where you store your scripts.
    $plugin_js_url = plugin_dir_url( __FILE__ ) . 'js/';

    // Register scripts - local versions
    wp_register_script( 'jquery', $plugin_js_url . 'jquery-1.12.4.js', array(), '1.12.4' );
    wp_register_script( 'datatables', $plugin_js_url . 'jquery.dataTables.min.js', array('jquery'), '1.10.15' );
    wp_register_script( 'fontawesome', $plugin_js_url . 'all.js', array(), '5.0.13' );
    wp_register_script( 'codemirror', $plugin_js_url . 'codemirror.min.js', array('jquery'), '5.56.0' );
    wp_register_script( 'wooproddel-calendar-script',  $plugin_js_url . 'wooproddel-calendar.js', array( 'jquery', 'datatables', 'codemirror' ), '1.0' );
    
    // Enqueue scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'datatables' );
    wp_enqueue_script( 'fontawesome' );
    wp_enqueue_script( 'codemirror' );
    wp_enqueue_script( 'wooproddel-calendar-script' );
 
    // Register and enqueue the style
    wp_register_style( 'wooproddel-calendar-style', plugins_url( '/css/wooproddel-calendar.css', __FILE__ ) );
    wp_enqueue_style( 'wooproddel-calendar-style' );
}

add_action( 'admin_enqueue_scripts', 'wooproddel_calendar_scripts' );



function wooproddel_calendar_shortcode_update(){ 
    global $wpdb;
    wooproddel_calendar_scripts();

  
  // Step 2: Fetch all the woocommerce orders that have the custom meta _wooproddel_delivery_date
  
  $sql = "SELECT p.ID as order_id, p.post_date as order_date, pm.meta_value as delivery_date, CONCAT(pm2.meta_value, ' ', pm3.meta_value) as customer_name,
				CASE WHEN DATEDIFF(pm.meta_value, CURRENT_DATE()) <= 2 THEN 'Yes' ELSE 'No' END AS two_days
                FROM {$wpdb->prefix}posts p
                INNER JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID
                LEFT JOIN {$wpdb->prefix}postmeta pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_billing_first_name'
                LEFT JOIN {$wpdb->prefix}postmeta pm3 ON pm3.post_id = p.ID AND pm3.meta_key = '_billing_last_name'
                WHERE p.post_type = %s
                AND pm.meta_key like %s
                GROUP BY order_id";
  
  $sql2 = "SELECT oi.order_item_id as order_item_id,  oi.order_item_name as product_name, p.ID as order_id, p.post_date as order_date, oim.meta_value as delivery_date, CONCAT(pm2.meta_value, ' ', pm3.meta_value) as customer_name, 
                CASE WHEN DATEDIFF(pm.meta_value, CURRENT_DATE()) <= 2 THEN 'Yes' ELSE 'No' END AS two_days 
                        FROM {$wpdb->prefix}posts p 
                        INNER JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID 
                        INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_id = p.ID 
                        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id 
                        LEFT JOIN {$wpdb->prefix}postmeta pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_billing_first_name' 
                        LEFT JOIN {$wpdb->prefix}postmeta pm3 ON pm3.post_id = p.ID AND pm3.meta_key = '_billing_last_name' 
                        WHERE p.post_type = %s 
                        AND oim.meta_key like %s
                        AND p.post_status != 'wc-split'
                        GROUP BY order_id, oi.order_item_id";
                        
  $resultorders = $wpdb->get_results( 
      $wpdb->prepare( 
          $sql, 
          'shop_order', 
          '_wooproddel_delivery_date'.'' 
      ), ARRAY_A );
                
  $resultproducts = $wpdb->get_results( 
      $wpdb->prepare( 
          $sql2, 
          'shop_order', 
          'Delivery Date'.'' 
      ), ARRAY_A );

$ordersarray = array_merge($resultorders,$resultproducts);
// Fetch dynamic timeslots from the settings
$timeslots = get_option('wooproddel_timeslots', []);
$timeslot_options = array('none' => __('Select a timeslot', 'wooproddel'));
foreach ($timeslots as $timeslot) {
    $timeslot_value = sprintf('%s-%s', $timeslot['start'], $timeslot['end']);
    $timeslot_options[$timeslot_value] = $timeslot_value;
}
  // Step 3: Create an HTML table that will look like an order management system
  $html = '';
  $html .= '<table class="wooproddel-calendar">';
  $html .= '<form action="'.$_SERVER["REQUEST_URI"].'" method="post" id="wooproddel_calendar_form">';

    // Step 4: Output the table header
  $html .= '<thead>';
  $html .= '<tr>';
  $html .= '<th>Order ID</th>';
  $html .= '<th>Order Status</th>';
  $html .= '<th>Customer Name</th>';
  $html .= '<th>Order Date</th>';
  $html .= '<th>Delivery Date</th>';
  $html .= '<th>Product Delivery Date</th>';
  $html .= '<th>Timeslot</th>';
  $html .= '<th>Product Name</th>';
  $html .= '<th>Product ID</th>';
  $html .= '<th>Within 2 Days?</th>';
  $html .= '</tr>';
  $html .= '</thead>';

  // Step 5: Output the table body
  $html .= '<style>
    table.wooproddel-calendar{
      width: 100%;
      border-collapse: collapse;
    }
    table.wooproddel-calendar th, 
    table.wooproddel-calendar td{
      border: 1px solid #ddd;
      padding: 8px;
    }
    table.wooproddel-calendar th{
      background-color: #ddd;
    }
  </style>';
  $html .= '<tbody>';

  $order_id_array = array();
  foreach ($ordersarray as $order) {
    // Sanitize the data
    $order_id = esc_html( $order['order_id'] );
    $order_status = esc_html( get_post_status( $order['order_id'] ) );
    $order_date = esc_html( $order['order_date'] );
    $customer_name = esc_html( $order['customer_name'] );
    $delivery_date = esc_html( get_post_meta( $order['order_id'], '_wooproddel_delivery_date', true ) );
    $product_delivery_date = esc_html( $order['delivery_date'] );
    if(isset( $order['product_name'])){
    $product_name = esc_html( $order['product_name'] );}
    if(isset( $order['order_item_id'])){
    $order_item_id = esc_html( $order['order_item_id'] );}
    // Check if the delivery date is within 2 days
    $two_days_from_now = strtotime( '+2 days' );
    $within_two_days = '';
    // Check if the delivery date is within 2 days
    $two_days_from_now = strtotime( '+2 days' );
    $within_two_days = '';
     if ( $two_days_from_now >= strtotime( $delivery_date ) && strtotime( $delivery_date ) >= strtotime( '-2 days' ) ) {
    $within_two_days = '<div style="background-color:#FF9800;color:#FFF;padding:2px 5px;">Yes</div>';
    } elseif ( strtotime( $delivery_date ) < strtotime( '-2 days' ) ) {
        $within_two_days = '<div style="background-color:#F44336;color:#FFF;padding:2px 5px;">Past Order</div>';
    } if ( $two_days_from_now >= strtotime( $product_delivery_date ) && strtotime( $product_delivery_date ) >= strtotime( '-2 days' ) ) {
    $within_two_days = '<div style="background-color:#FF9800;color:#FFF;padding:2px 5px;">Yes</div>';
    } elseif ( strtotime( $product_delivery_date ) < strtotime( '-2 days' ) ) {
        $within_two_days = '<div style="background-color:#F44336;color:#FFF;padding:2px 5px;">Past Order</div>';
    }
    else {
        $within_two_days = '<div style="background-color:#4CAF50;color:#FFF;padding:2px 5px;">No</div>';
    }
    // Set the background color for the order status
    $bg_color = '';
    switch ( $order_status ) {
      case 'wc-completed':
        $bg_color = '#4CAF50';
        break;
      case 'wc-pending':
        $bg_color = '#FF9800';
        break;
      case 'wc-processing':
        $bg_color = '#2196F3';
        break;
      case 'wc-on-hold':
        $bg_color = '#9E9E9E';
        break;
    }
        // Output the table row
    if(!in_array($order_id,$order_id_array)){
        $html .= '<tr>';
        $html .= '<td>' . esc_html( $order_id ) . '</td>';
        $html .= '<td style="background-color:' . esc_attr( $bg_color ) . ';">' . esc_html( $order_status ) . '</td>';
        $html .= '<td>' . esc_html( $customer_name ) . '</td>';
        $html .= '<td>' . esc_html( $order_date ) . '</td>';
        $html .= '<td><input type="date" id="'.esc_attr( $order_id ).'" name="delivery_date['.esc_attr( $order_id ).']" value="'.esc_attr( $delivery_date ).'"></td>';
        $html .= '<td></td>';
        $html .= '<td>';
        $html .= '<select class="timeslot-dropdown" name="timeslot[' . esc_attr($order_id) . ']">';
        foreach ($timeslot_options as $value => $label) {
            $selected = selected(get_post_meta($order_id, 'wooproddel_delivery_timeslot', true), $value, false);
            $html .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';

        
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td>' . wp_kses_post( $within_two_days ) . '</td>';
        $html .= '</tr>';
        array_push($order_id_array,$order_id);
    }
    if($order['order_item_id']){
        $html .= '<tr>';
        $html .= '<td>' . esc_html( $order_id ) . '</td>';
        $html .= '<td style="background-color:' . esc_attr( $bg_color ) . ';">' . esc_html( $order_status ) . '</td>';
        $html .= '<td>' . esc_html( $customer_name ) . '</td>';
        $html .= '<td>' . esc_html( $order_date ) . '</td>';
        $html .= '<td></td>';
        $html .= '<td><input type="date" id="'.esc_attr( $order_item_id ).'" name="product_delivery_date['.esc_attr( $order_item_id ).']" value="'.esc_attr( $product_delivery_date ).'"></td>';
        $html .= '<td>';
        $html .= '<select class="timeslot-dropdown" name="timeslot[' . esc_attr($order_id) . ']">';
        foreach ($timeslot_options as $value => $label) {
            $selected = selected(get_post_meta($order_id, 'wooproddel_delivery_timeslot', true), $value, false);
            $html .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';

        $html .= '<td>' . esc_html( $product_name ) . '</td>';
        $html .= '<td>' . esc_html( $order_item_id ) . '</td>';
        $html .= '<td>' . wp_kses_post( $within_two_days ) . '</td>';
        $html .= '</tr>';
    }
  }
  
  $html .= '</tbody>';
  ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
  <input type="hidden" name="action" value="wooproddel_export_to_excel" />
  
  <button type="submit" class="button button-primary">Export to Excel</button>
</form><?PHP
  $html .= '</table>';
  $html .= '<input type="submit" id="submit-btn" name="update_delivery_date" value="Save Changes" class="button">';
  //$html .= '<input type="submit" id="submit-btn" value="Save Changes" name="update_delivery_date" class="button button-primary"/>';
  $html .= '</form>';
  

  return $html;
  
}

function update_delivery_date_handler() {  
    global $wpdb;

    // Get the data from the AJAX request
    $delivery_date = sanitize_text_field($_POST['delivery_date']);
    $product_delivery_date = sanitize_text_field($_POST['product_delivery_date']);
    $order_id = intval($_POST['order_id']);
    $timeslot = sanitize_text_field($_POST['timeslot']);
    
    // Step 6: Process the form
    if ( isset( $delivery_date ) ) {
      // Update the post meta for the delivery date
      update_post_meta( $order_id, '_wooproddel_delivery_date', $delivery_date );
    }
    if ( isset( $product_delivery_date ) ) {
      // Update the order item meta for the product delivery date
       $update_query = 'UPDATE '.$wpdb->prefix.'woocommerce_order_itemmeta SET meta_value="'.$product_delivery_date.'" WHERE order_item_id="'.$order_id.'" AND meta_key="Delivery Date"';
      $wpdb->query($update_query);
    }
    if ( isset( $timeslot ) ) {
        update_post_meta($order_id, 'wooproddel_delivery_timeslot', $timeslot);
    }

    
    // Log any errors that may occur
    if ( is_wp_error($wpdb->last_error) ) {
        error_log( is_wp_error($wpdb->last_error)->get_error_message() );
    }
}

?>
