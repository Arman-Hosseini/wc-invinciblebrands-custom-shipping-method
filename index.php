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
    }
}

if ( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) )
    // Show admin notice If the WooCommerce plugin isn't active
    add_action('admin_notices', function () {
        printf('<div class="notice notice-warning"><p>To use the <strong>%s plugin</strong>, you must activate the WooCommerce plugin!</p></div>', basename(__DIR__));
    });
else
    new WC_InvincibleBrands_Custom_ShippingMethod_Field;
