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
        /**
         * WC_InvincibleBrands_Custom_ShippingMethod_Field constructor.
         */
        public function __construct()
        {
        }
    }
}

new WC_InvincibleBrands_Custom_ShippingMethod_Field;
