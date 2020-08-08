<?php
/**
 * Plugin Name: Custom Carrier ID Field for Shipping Methods
 * Description: Creates a new Carrier ID input field for shipping methods Flat-rate and Free-shipping.
 * Plugin URI: https://www.invinciblebrands.com/
 * Version: 1.0
 * Author: Invincible Brands
 * Author URI: https://www.invinciblebrands.com/
 */

defined( 'ABSPATH' ) || exit;

if ( !class_exists('WC_InvincibleBrands_Custom_ShippingMethod_Field') )
{
    class WC_InvincibleBrands_Custom_ShippingMethod_Field
    {
        private static $our_shipping_methods = ['flat_rate', 'free_shipping'];

        /**
         * WC_InvincibleBrands_Custom_ShippingMethod_Field constructor.
         */
        public function __construct()
        {
            $this->initHooks();
        }

        /**
         * Initialize the Hooks.
         */
        private function initHooks()
        {
            add_action('woocommerce_init', [$this, 'shipping_methods_add_carrier_id']);
            add_action('woocommerce_order_status_processing', [$this, 'order_processed']);
        }

        /**
         * Add Carrier Id input field to self::$our_shipping_methods
         */
        public function shipping_methods_add_carrier_id()
        {
            $shipping_methods = WC()->shipping()->get_shipping_methods();
            foreach ($shipping_methods as $shipping_method) {
                if ( in_array($shipping_method->id, self::$our_shipping_methods) )
                    add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, function ($fields) {
                        $fields['carrier_id'] = [
                            'title'       => 'Carrier ID',
                            'type'        => 'text',
                        ];

                        return $fields;
                    });
            }
        }

        /**
         * This method stores _carrier_id ​as a meta key for those orders that
         * use self::$our_shipping_methods and their status is processing (wc-process).
         *
         * @param int $order_id
         */
        public function order_processed($order_id) {
            $order = wc_get_order($order_id);
            $order_items = $order->get_items('shipping');

            $shipping_item_id = key($order_items);
            $shipping_item    = $order->get_item($shipping_item_id);
            $shipping_method  = $shipping_item->get_meta('method_id');

            if ( in_array($shipping_method, self::$our_shipping_methods) ) {
                $shipping_method_instance_id = $shipping_item->get_meta('instance_id');
                $shipping = null;

                switch ( $shipping_method ) {
                    case self::$our_shipping_methods[0]:
                        $shipping = new WC_Shipping_Flat_Rate($shipping_method_instance_id);
                        break;
                    case self::$our_shipping_methods[1]:
                        $shipping = new WC_Shipping_Free_Shipping($shipping_method_instance_id);
                        break;
                }

                $carrier_id = $shipping->get_option('carrier_id');
                if ( !empty($carrier_id) ) {
                    $order->update_meta_data('_carrier_id', $carrier_id);
                    $order->save_meta_data();
                }
            }
        }
    }
}

if ( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) )
    // Show admin notice If the WooCommerce plugin isn't active
    add_action('admin_notices', function () {
        printf('<div class="notice notice-warning"><p>To use the <strong>%s plugin</strong>, you must activate the WooCommerce plugin!</p></div>', basename(__DIR__));
    });
else
    new WC_InvincibleBrands_Custom_ShippingMethod_Field;
