<?php

/*

Copyright (c) Markeaze Inc. https://markeaze.com

This file is part of the markeaze-for-woocommerce plugin created by Markeaze.

Repository: https://github.com/markeaze/markeaze-woocommerce
Documentation: https://github.com/markeaze/markeaze-woocommerce/blob/master/README.md

*/

class Markeaze {

  private static
    $initiated = false,
    $plugin_assets_path,
    $user_id,
    $userFirstName,
    $userLastName,
    $userEmail,
    $userPhone,
    $userDateOfBirth,
    $userGender,
    $log = false
  ;

  public static function init() {
    if (!self::$initiated ) {
      self::$plugin_assets_path = MARKEAZE_PLUGIN_URL . 'assets/';
      self::init_hooks();
      self::$initiated = true;
      load_plugin_textdomain('markeaze', false, 'markeaze/languages');
    }
  }

  public static function activated_action_handler($plugin) {
    $name = plugin_basename( trim($plugin) );
  	if ($name == 'markeaze/markeaze.php') {
  	  exit( wp_safe_redirect( admin_url( 'admin.php?page=markeaze' ) ) );
  	}
  }

  /**
   * Initializes WordPress hooks
   */
  private static function init_hooks() {
    // Calling a function add administrative menu.

    add_action( 'admin_menu', array('Markeaze', 'plgn_add_pages') );

    if (!is_admin()) {
      add_action( 'wp_head', array('Markeaze', 'markeaze_main') );
      add_action( 'woocommerce_before_single_product', array('Markeaze', 'productView') );
    }

    add_action( 'woocommerce_cart_updated', array('Markeaze', 'submitCart'));
    add_action( 'woocommerce_checkout_order_processed', array('Markeaze', 'submitOrder') );
    add_action( 'woocommerce_order_status_changed', array('Markeaze', 'stateOrder') );
    add_action( 'wp_trash_post', array('Markeaze', 'deleteOrder') );

    register_uninstall_hook( __FILE__, array('Markeaze', 'delete_options') );
  }

  // Function for delete options
  public static function delete_options() {
    delete_option('markeaze_plgn_options');
  }

  public static function plgn_add_pages() {
    add_menu_page(
      __( 'Markeaze', 'markeaze' ),
      __( 'Markeaze', 'markeaze' ) . (self::get_app_key() ? '' : '<span class="awaiting-mod">!</span>'),
      'administrator',
      'markeaze',
      array( 'Markeaze', 'plgn_settings_page' ),
      self::$plugin_assets_path . 'icon.svg'
    );
    // Call register settings function
    add_action( 'admin_init', array('Markeaze', 'plgn_settings') );
  }

  public static function plgn_options_default() {
    return array(
      'markeaze_key' => '',
      'only_product_id' => '1'
    );
  }

  public static function plgn_settings()
  {
    $plgn_options_default = self::plgn_options_default();

    if (!get_option('markeaze_plgn_options')) {
      add_option('markeaze_plgn_options', $plgn_options_default, '', 'yes');
    }

    $plgn_options = get_option('markeaze_plgn_options');
    $plgn_options = array_merge($plgn_options_default, $plgn_options);

    update_option('markeaze_plgn_options', $plgn_options);
  }

  // Function formed content of the plugin's admin page.
  public static function plgn_settings_page() {
    global $wp_session;

    $markeaze_plgn_options = self::get_params();
    $markeaze_plgn_options_default = self::plgn_options_default();
    $message = !empty($_GET['success']) ? __('Settings saved', 'markeaze') : null;

    if (
      isset($_REQUEST['markeaze_plgn_form_submit'])
      && check_admin_referer(plugin_basename(dirname(__DIR__)), 'markeaze_plgn_nonce_name')
    ) {
      foreach($markeaze_plgn_options_default as $k => $v) {
        $markeaze_plgn_options[$k] = trim(self::request($k, $v));
      }

      update_option('markeaze_plgn_options', $markeaze_plgn_options);

      exit( wp_safe_redirect( admin_url( 'admin.php?page=markeaze&success=true' ) ) );
    }

    $options = array(
      'markeaze_plgn_options' => $markeaze_plgn_options,
      'message' => $message
    );

    echo self::loadTPL('adminform', $options);
  }

  private static function loadTPL($name, $options) {
    $tmpl = ( MARKEAZE_PLUGIN_DIR .'tmpl/' . $name . '.php');

    if (!is_file($tmpl)) return __('Error Load Template', 'markeaze');

    extract($options, EXTR_PREFIX_SAME, 'markeaze');

    ob_start();

    include $tmpl;

    return ob_get_clean();
  }

  private static function request($name, $default = null) {
    return (isset($_REQUEST[$name])) ? sanitize_text_field($_REQUEST[$name]) : $default;
  }

  public static function markeaze_main() {
    if (self::get_app_key()) {
      $visitor = array();
      $user_id = wp_get_current_user()->ID;

      if ($user_id > 0) {
        self::updateUserInfo();

        $visitor['client_id'] = (string) $user_id;
        if(!empty(self::$userFirstName))        $visitor['first_name']      = self::$userFirstName;
        if(!empty(self::$userLastName))         $visitor['last_name']       = self::$userLastName;
        if(!empty(self::$userEmail))            $visitor['email']           = self::$userEmail;
        if(!empty(self::$userPhone))            $visitor['phone']           = self::$userPhone;
        if(!empty(self::$userDateOfBirth))      $visitor['date_of_birth']   = self::$userDateOfBirth;
        if(!empty(self::$userGender))           $visitor['gender']          = self::$userGender;

        do_action( 'markeaze_visitor_info', $visitor );
      }

?>
<script type="text/javascript">
  (function(w,d,c){w[c]=w[c]||function(){(w[c].q=w[c].q||[]).push(arguments)};var t = document.cookie.match(new RegExp('(^| )mkz_version=([^;]+)'));var h = 'https://cdn.jsdelivr.net/gh/markeaze/markeaze-js-tracker@'+(t&&t[2]||'latest')+'/dist/mkz.js';var s = d.createElement('script');s.type = 'text/javascript';s.async = true;s.charset = 'utf-8';s.src = h;var x = d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);})(window,document,'mkz');

  mkz('watch', 'url.change', function() {
    mkz('trackPageView');
  });

  mkz('appKey', '<?= self::get_app_key() ?>');

  <?php if (count($visitor) > 0): ?>mkz('setVisitorInfo', <?= json_encode($visitor) ?>);<?php endif; ?>
</script>
<?php

    }
  }

  /** Submit product
   * @param $id
   * @param $name
   * @throws Exception
   */
  public static function productView() {
    if (self::get_app_key()) {
      global $wp_query;
      $uri = get_permalink($wp_query->post);
      $product_cats = wp_get_post_terms( $wp_query->post->ID, 'product_cat', array('fields' => 'ids') );
      $category_id = (is_array($product_cats) && count($product_cats)) ? $product_cats[0] : 0;
      $product_id = $wp_query->post->ID;
      $product = wc_get_product($product_id);
      $markeaze_plgn_options = self::get_params();

      if (!$markeaze_plgn_options['only_product_id'] && $product->get_type() == 'variable') {
        $available_variations = $product->get_available_variations();
        if (count($available_variations) > 0) {
          $variant = array_shift($available_variations);
          $product_id = $variant['variation_id'];
        }
      }

      $offer_data = array(
        'variant_id' => (string) $product_id,
        'name' => $wp_query->post->post_title,
        'main_image_url' => $uri
      );

      $category_data = array(
        'uid' => $category_id
      );

?>
<script type="text/javascript">
  mkz('setOfferView', <?= json_encode($offer_data) ?>);
  mkz('setCategoryView', <?= json_encode($category_data) ?>);
</script>
<?php

    }
  }

  /** Submit order state to markeaze
   * @param $order_id
   */
  public static function stateOrder($order_id) {
    $order = self::getOrder($order_id);

    if (empty($order)) return;

    if (!($tracker = self::init_tracker())) return;

    // Support Woocommerce 2.6.4
    if (property_exists($order, 'order_date') and property_exists($order, 'modified_date')) {
      $date_created = $order->order_date;
      $date_modified = $order->modified_date;
    } else {
      $data = $order->get_data();
      // if order is created in the admin panel
      $format = 'd.m.y H:i:s';
      $date_created = $data['date_created']->date($format);
      $date_modified = $data['date_modified']->date($format);
    }

    if (is_admin_bar_showing() and $date_created == $date_modified) {
      self::submitOrder($order_id);
    } else {
      if ($order->get_status() === 'cancelled') {
        $tracker->track('order_cancel', array(
          'order_uid' => (string) $order_id
        ));
      } else {
        $tracker->track('order_update', self::getOrderData($order_id));
      }
    }
  }

  /** Submit order delete to markeaze
   * @param $order_id
   */
  public static function deleteOrder($order_id) {
    $order = self::getOrder($order_id);

    if (empty($order)) return;

    if (!($tracker = self::init_tracker())) return;

    $tracker->track('order_cancel', array(
      'order_uid' => $order_id
    ));
  }

  /** Submit order to markeaze
   * @param $order_id
   */
  public static function submitOrder($order_id) {
    if (!($tracker = self::init_tracker())) return;

    self::updateUserInfo();

    $order = self::getOrder($order_id);

    if (is_admin_bar_showing()) {
      self::$user_id = $order->customer_user ? $order->customer_user : false;
    }

    $visitor = array();
    if (self::$user_id) {
      $visitor['client_id'] = (string) self::$user_id;
    }
    $first_name = self::getValue(self::getMetaValue($order, 'billing_first_name'), self::$userFirstName);
    if ($first_name !== false){
      $visitor['first_name'] = $first_name;
    }
    $last_name = self::getValue(self::getMetaValue($order, 'billing_last_name'), self::$userLastName);
    if ($last_name !== false){
      $visitor['last_name'] = $last_name;
    }
    $email = self::getValue(self::getMetaValue($order, 'billing_email'), self::$userEmail);
    if ($email !== false){
      $visitor['email'] = $email;
    }
    $phone = self::getValue(self::getMetaValue($order, 'billing_phone'), self::$userPhone);
    if ($phone !== false){
      $visitor['phone'] = $phone;
    }
    if (!empty(self::$userDateOfBirth)){
      $visitor['date_of_birth'] = self::$userDateOfBirth;
    }
    if (!empty(self::$userGender)){
      $visitor['gender'] = self::$userGender;
    }
    do_action( 'markeaze_visitor_info', $visitor );

    $tracker->set_visitor_info($visitor);
    $tracker->track('order_create', self::getOrderData($order_id));
  }

  public static function getOrderData($order_id) {
    $order = self::getOrder($order_id);

    $items = array();
    $line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
    $total = $order->get_total();
    $shipping = (method_exists($order, 'get_total_shipping')) ? $order->get_total_shipping() : 0;
    $order_total = $total - $shipping;

    if (is_array($line_items) && count($line_items)) {
      $markeaze_plgn_options = self::get_params();
      foreach ($line_items as $item_id => $item) {
        $pid = (!empty($item['variation_id']) && !$markeaze_plgn_options['only_product_id'])
          ? $item['variation_id'] : $item['product_id'];
        $price = $item['line_subtotal']
          / (float)$item['qty'];
        $product = wc_get_product( $pid );
        $item = array(
          'variant_id' => (string) $pid,
          'name' => $item['name'],
          'qnt' => (float) $item['qty'],
          'price' => (float) $price
        );

        if ($product) {
          $image_url = wp_get_attachment_url( $product->get_image_id() );
          if ($image_url) $item['main_image_url'] = (string) $image_url;
          $item['url'] = (string) $product->get_permalink();
        }

        $items[] = $item;
      }
    }

    return array(
      'order_uid' => (string) $order_id,
      'total' => (float) $order_total,
      'items' => $items,
      'fulfillment_status' => (string) wc_get_order_status_name($order->get_status()),
      'financial_status' => __($order->is_paid() ? 'Paid' : 'Not paid', 'markeaze'),
      'payment_method' => (string) $order->get_payment_method_title(),
      'shipping_method' => (string) $order->get_shipping_method()
    );
  }

  private static function getMetaValue($order, $field) {
    $method = "get_{$field}";
    // Support Woocommerce 2.6.4
    return method_exists($order, $method) ? $order->$method() : get_user_meta(get_current_user_id(), $field, true);
  }

  private static function getValue($value, $default) {
    if (empty($value) && empty($default)) return false;

    return (!empty($value)) ? $value : $default;
  }

  /** Submit cart to markeaze
   * @param $order_number
   * @param $order_total
   * @param $items
   */
  public static function submitCart() {
    if (!($tracker = self::init_tracker())) return;

    if (function_exists('WC')) $wc = WC();
    else {
      global $woocommerce;
      $wc = $woocommerce;
    }

    self::updateUserInfo();

    $visitor = array();
    if (self::$user_id) {
      $visitor['client_id'] = (string) self::$user_id;
    }
    if (!empty(self::$userFirstName)){
      $visitor['first_name'] = self::$userFirstName;
    }
    if (!empty(self::$userLastName)){
      $visitor['last_name'] = self::$userLastName;
    }
    if (!empty(self::$userEmail)){
      $visitor['email'] = self::$userEmail;
    }
    if (!empty(self::$userPhone)){
      $visitor['phone'] = self::$userPhone;
    }
    if (!empty(self::$userDateOfBirth)){
      $visitor['date_of_birth'] = self::$userDateOfBirth;
    }
    if (!empty(self::$userGender)){
      $visitor['gender'] = self::$userGender;
    }
    do_action( 'markeaze_visitor_info', $visitor );

    $cart = $wc->cart->get_cart();
    $sessionCartValue = unserialize($wc->session->get('markeaze_cart_value', ''));

    self::log('Event upldate cart '. date('Y-m-d h:i:s'));
    self::log('$sessionCartValue');
    self::log($sessionCartValue);

    $items = array();

    if (count($cart)) {
      self::log('Count cart = '.count($cart));

      $markeaze_plgn_options = self::get_params();
      foreach ($cart as $k => $v) {
        $pid = (!empty($v['variation_id']) && !$markeaze_plgn_options['only_product_id'])
          ? $v['variation_id'] : $v['product_id'];
        $price = $v['data']->get_price();
        $product =  wc_get_product($v['data']->get_id());
        $item = array(
          'variant_id' => (string) $pid,
          'name' => $product->get_title(),
          'qnt' => (float) $v['quantity'],
          'price' => (float) $price
        );

        if ($product) {
          $image_url = wp_get_attachment_url( $product->get_image_id() );
          if ($image_url) $item['main_image_url'] = (string) $image_url;
          $item['url'] = (string) $product->get_permalink();
        }

        $items[] = $item;
      }
    }

    if ($sessionCartValue != $items) {
      self::log('Cart changed: '.serialize($items));
      $tracker->set_visitor_info($visitor);
      $tracker->track('cart_update', array(
        'items' => $items
      ));

      $wc->session->set('markeaze_cart_value', serialize($items));
    }
  }

  public static function getOrder($order_id) {
    return function_exists('wc_get_order') ? wc_get_order( $order_id ) : new WC_Order( $order_id );
  }

  private static function updateUserInfo() {
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_data = get_user_meta( $user_id );

    self::$user_id = $user_id;

    if (!empty($user_data['first_name'][0])) self::$userFirstName = $user_data['first_name'][0];
    if (!empty($user_data['last_name'][0])) self::$userLastName = $user_data['last_name'][0];
    if (!empty($current_user->data->user_email)) self::$userEmail = $current_user->data->user_email;
    if (!empty($user_data['billing_phone'][0])) self::$userPhone = $user_data['billing_phone'][0];
  }

  private static function get_params() {
    static $params;
    if (empty($params)) {
      $params = get_option('markeaze_plgn_options');
    }
    return $params;
  }

  private static function get_app_key() {
    $markeaze_plgn_options = self::get_params();
    return empty($markeaze_plgn_options['markeaze_key']) ? null : $markeaze_plgn_options['markeaze_key'];
  }

  private static function init_tracker() {
    $app_key = self::get_app_key();
    if (!$app_key) return false;
    require_once MARKEAZE_PLUGIN_DIR . 'vendor/mkz.php';
    $tracker = new Mkz($app_key);
    return $tracker;
  }

  private static function log($data) {
    if (self::$log) {
      $data = print_r($data, true);
      $file = MARKEAZE_PLUGIN_DIR .'log/log.txt';
      file_put_contents($file, PHP_EOL . $data, FILE_APPEND);
    }
  }
}
