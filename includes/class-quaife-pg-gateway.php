<?php

/**
 * Called as needed
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 */

/**
 * Add Quaife Payments (as WC_Payment_Gateway extension)
 *
 * @since      1.1.0
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */

use Automattic\Jetpack\Constants;

class WC_Gateway_Quaife_PG extends WC_Payment_Gateway
{

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        // Setup general properties. (payment method title, description...)
        $this->setup_properties();

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title        = $this->get_option('title');
        $this->description  = $this->get_option('description');
        $this->instructions = $this->get_option('instructions', $this->description);
        $this->enable_for_methods = $this->get_option('enable_for_methods', array());
        $this->enable_for_virtual = $this->get_option('enable_for_virtual', 'yes') === 'yes';


        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
        add_filter('woocommerce_payment_complete_order_status', array($this, 'change_payment_complete_order_status'), 10, 3);

        // register a webhook here
        add_action('woocommerce_api_quaife', array($this, 'webhook'));

        // Customer Emails
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
    }

    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties()
    {
        $this->id                 = 'woo-quaife-pg';
        $this->icon               = apply_filters('woocommerce_quaife_icon', '');
        $this->has_fields         = true; // in case a custom credit card form is needed
        $this->method_title       = __('Quaife Payments', 'woo-quaife-pg');
        $this->method_description = __('Allows Quaife Payment Processor payments. Works as Hosted Payment Page', 'woo-quaife-pg');

        // gateways can support subscriptions, refunds, saved payment methods
        $this->supports = array(
            'products'
        );
    }


    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields()
    {

        $this->form_fields = apply_filters('wc_quaife_form_fields', array(

            'enabled' => array(
                'title'   => __('Enable/Disable', 'woo-quaife-pg'),
                'type'    => 'checkbox',
                'label'   => __('Enable Quaife Payments', 'woo-quaife-pg'),
                'default' => 'yes'
            ),

            'title' => array(
                'title'       => __('Title', 'woo-quaife-pg'),
                'type'        => 'text',
                'description' => __('This controls the title for the payment method the customer sees during checkout.', 'woo-quaife-pg'),
                'default'     => __('Quaife Payments', 'woo-quaife-pg'),
                'desc_tip'    => true,
            ),

            'description' => array(
                'title'       => __('Description', 'woo-quaife-pg'),
                'type'        => 'textarea',
                'description' => __('Payment method description that the customer will see on your checkout.', 'woo-quaife-pg'),
                'default'     => __('Quaife Payment Processor - numerous payment options for you.', 'woo-quaife-pg'),
                'desc_tip'    => true,
            ),

            'instructions' => array(
                'title'       => __('Instructions', 'woo-quaife-pg'),
                'type'        => 'textarea',
                'description' => __('Instructions that will be added to the thank you page and emails.', 'woo-quaife-pg'),
                'default'     => '',
                'desc_tip'    => true,
            ),

            'enable_for_methods' => array(
                'title'             => __('Enable for shipping methods', 'woo-quaife-pg'),
                'type'              => 'multiselect',
                'class'             => 'wc-enhanced-select',
                'css'               => 'width: 400px;',
                'default'           => '',
                'description'       => __('If Quaife is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'woo-quaife-pg'),
                'options'           => $this->load_shipping_method_options(),
                'desc_tip'          => true,
                'custom_attributes' => array(
                    'data-placeholder' => __('Select shipping methods', 'woo-quaife-pg'),
                ),
            ),

            'enable_for_virtual' => array(
                'title'   => __('Accept for virtual orders', 'woo-quaife-pg'),
                'label'   => __('Accept Quaife if the order is virtual', 'woo-quaife-pg'),
                'type'    => 'checkbox',
                'default' => 'yes',
            ),

            /* 'icon' => array(
                'title'    => esc_html__('Logo', 'woo-quaife-pg'),
                'desc'     => esc_html__('This controls the image which the user sees during checkout.', 'woo-quaife-pg'),
                'type'     => 'icon',
                'default'  => '',
                'desc_tip' => true,
            ), */
        ));
    }

    /**
     * add field type to settings
     * https://unax.org/add-custom-field-at-woocommerce-payment-gateways-settings-page/
     */
    public function generate_icon_html($key, $data)
    {
        $field_key = $this->get_field_key($key);
        $defaults  = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
        );

        $data = wp_parse_args($data, $defaults);

        $data['id']     = 'woocommerce_' . $this->id . '_icon';
        $data['value']     = $this->get_option('icon');

        ob_start();
?>

        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($data['id']); ?>">
                    <?php echo esc_html($data['title']); ?>
                    <span class="woocommerce-help-tip" data-tip="<?php echo esc_html($data['desc']); ?>"></span>
                </label>
            </th>
            <td class="forminp forminp-<?php echo esc_attr($data['type']) ?>">
                <input type="hidden" id="<?php echo esc_attr($data['id']); ?>" name="<?php echo esc_attr($data['id']); ?>" value="<?php echo esc_attr($data['value']); ?>">
                <img src="<?php echo !empty($data['value']) ? esc_url(wp_get_attachment_image_url($data['value'], 'medium')) : ''; ?>">
                <p class="controls">
                    <button class="button-primary add-media">
                        <?php esc_html_e('Add Logo', 'text-domain'); ?>
                    </button>
                    <button class="button-secondary remove-media">
                        <?php esc_html_e('Remove Logo', 'text-domain'); ?>
                    </button>
                </p>
            </td>
        </tr>

<?php
        return ob_get_clean();
    }

    /**
     * Check If The Gateway Is Available For Use.
     *
     * @return bool
     */
    public function is_available()
    {
        $order          = null;
        $needs_shipping = false;

        // Test if shipping is needed first.
        if (WC()->cart && WC()->cart->needs_shipping()) {
            $needs_shipping = true;
        } elseif (is_page(wc_get_page_id('checkout')) && 0 < get_query_var('order-pay')) {
            $order_id = absint(get_query_var('order-pay'));
            $order    = wc_get_order($order_id);

            // Test if order needs shipping.
            if ($order && 0 < count($order->get_items())) {
                foreach ($order->get_items() as $item) {
                    $_product = $item->get_product();
                    if ($_product && $_product->needs_shipping()) {
                        $needs_shipping = true;
                        break;
                    }
                }
            }
        }

        $needs_shipping = apply_filters('woocommerce_cart_needs_shipping', $needs_shipping);

        // Virtual order, with virtual disabled.
        if (!$this->enable_for_virtual && !$needs_shipping) {
            return false;
        }

        // Only apply if all packages are being shipped via chosen method, or order is virtual.
        if (!empty($this->enable_for_methods) && $needs_shipping) {
            $order_shipping_items            = is_object($order) ? $order->get_shipping_methods() : false;
            $chosen_shipping_methods_session = WC()->session->get('chosen_shipping_methods');

            if ($order_shipping_items) {
                $canonical_rate_ids = $this->get_canonical_order_shipping_item_rate_ids($order_shipping_items);
            } else {
                $canonical_rate_ids = $this->get_canonical_package_rate_ids($chosen_shipping_methods_session);
            }

            if (!count($this->get_matching_rates($canonical_rate_ids))) {
                return false;
            }
        }

        return parent::is_available();
    }

    /**
     * Loads all of the shipping method options for the enable_for_methods field.
     *
     * @return array
     */
    private function load_shipping_method_options()
    {
        // Since this is expensive, we only want to do it if we're actually on the settings page.
        if (!$this->is_accessing_settings()) {
            return array();
        }

        $data_store = WC_Data_Store::load('shipping-zone');
        $raw_zones  = $data_store->get_zones();

        foreach ($raw_zones as $raw_zone) {
            $zones[] = new WC_Shipping_Zone($raw_zone);
        }

        $zones[] = new WC_Shipping_Zone(0);

        $options = array();
        foreach (WC()->shipping()->load_shipping_methods() as $method) {

            $options[$method->get_method_title()] = array();

            // Translators: %1$s shipping method name.
            $options[$method->get_method_title()][$method->id] = sprintf(__('Any &quot;%1$s&quot; method', 'woo-quaife-pg'), $method->get_method_title());

            foreach ($zones as $zone) {

                $shipping_method_instances = $zone->get_shipping_methods();

                foreach ($shipping_method_instances as $shipping_method_instance_id => $shipping_method_instance) {

                    if ($shipping_method_instance->id !== $method->id) {
                        continue;
                    }

                    $option_id = $shipping_method_instance->get_rate_id();

                    // Translators: %1$s shipping method title, %2$s shipping method id.
                    $option_instance_title = sprintf(__('%1$s (#%2$s)', 'woo-quaife-pg'), $shipping_method_instance->get_title(), $shipping_method_instance_id);

                    // Translators: %1$s zone name, %2$s shipping method instance name.
                    $option_title = sprintf(__('%1$s &ndash; %2$s', 'woo-quaife-pg'), $zone->get_id() ? $zone->get_zone_name() : __('Other locations', 'woo-quaife-pg'), $option_instance_title);

                    $options[$method->get_method_title()][$option_id] = $option_title;
                }
            }
        }

        return $options;
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
        if ($this->instructions) {
            echo wpautop(wptexturize($this->instructions));
        }
    }


    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {

        if ($this->instructions && !$sent_to_admin && $this->id === $order->payment_method && $order->has_status('on-hold')) {
            echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
        }
    }


    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id)
    {

        $order = wc_get_order($order_id);

        if ($order->get_total() > 0) {
            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Awaiting Quaife payment', 'woo-quaife-pg'));
        } else {
            $order->payment_complete();
        }

        // Reduce stock levels
        $order->reduce_order_stock();

        // Remove cart
        WC()->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result'     => 'success',
            'redirect'    => $this->get_return_url($order)
        );
    }

    /**
     * Checks to see whether or not the admin settings are being accessed by the current request.
     *
     * @return bool
     */
    private function is_accessing_settings()
    {
        if (is_admin()) {
            // phpcs:disable WordPress.Security.NonceVerification
            if (!isset($_REQUEST['page']) || 'wc-settings' !== $_REQUEST['page']) {
                return false;
            }
            if (!isset($_REQUEST['tab']) || 'checkout' !== $_REQUEST['tab']) {
                return false;
            }
            if (!isset($_REQUEST['section']) || 'woo-quaife-pg' !== $_REQUEST['section']) {
                return false;
            }
            // phpcs:enable WordPress.Security.NonceVerification

            return true;
        }

        /* if (Constants::is_true('REST_REQUEST')) {
            global $wp;
            if (isset($wp->query_vars['rest_route']) && false !== strpos($wp->query_vars['rest_route'], '/payment_gateways')) {
                return true;
            }
        } */

        return false;
    }

    /**
     * Change payment complete order status to completed for COD orders.
     *
     * @since  3.1.0
     * @param  string         $status Current order status.
     * @param  int            $order_id Order ID.
     * @param  WC_Order|false $order Order object.
     * @return string
     */
    public function change_payment_complete_order_status($status, $order_id = 0, $order = false)
    {
        if ($order && 'woo-quaife-pg' === $order->get_payment_method()) {
            $status = 'completed';
        }
        return $status;
    }
} // end \WC_Gateway_Quaife_PG class