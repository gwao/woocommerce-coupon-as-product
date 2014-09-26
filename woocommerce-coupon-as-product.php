<?php
/**
 * Plugin Name: WooCommerce Coupon as Product
 * Description:  Let coupon can be a product and add into other woocommerce cart.
 * Author: Zheng Xian Qiu
 * Version: 1.0
 * Author URI: http://frost.tw/
 * Text Domain: woocommerce_coupon_as_product
 *
 * @package Coupon_As_Product
 * @version 1.0
*/

namespace MOHO;

if( ! defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

if( !defined( 'WC_COUPON_AS_PRODUCT_PATH') ) {
    define('WC_COUPON_AS_PRODUCT_PATH', plugin_dir_path(__FILE__));
}

include WC_COUPON_AS_PRODUCT_PATH . '/includes/ProductMetaBox.class.php';
include WC_COUPON_AS_PRODUCT_PATH . '/includes/PaymentComplete.class.php';
include WC_COUPON_AS_PRODUCT_PATH . '/includes/ReviewCart.class.php';
include WC_COUPON_AS_PRODUCT_PATH . '/includes/ExtraFields.class.php';

if ( ! class_exists('CouponAsProduct') ) {
    /**
     * Class CouponAsProduct
     * @author Zheng Xiqn Qiu
     */
    final class CouponAsProduct
    {
        /**
         * @var CouponRegiter The single instance of the class
         */
        protected static $_instance = null;

        /**
         * CouponAsProduct Constructor
         *
         * @access private
         * @return CouponAsProduct
         */
        private function __construct()
        {
            if(self::isWooCommerceActive()) {
                // Initialize API
                // TODO: Add API Class and implement api feature

                // Hooks
                add_action( 'woocommerce_process_product_meta_simple', __NAMESPACE__ . '\CouponAsProduct\ProductMetaBox::saveCouponProduct' );
                add_action( 'woocommerce_product_options_general_product_data', __NAMESPACE__ . '\CouponAsProduct\ProductMetaBox::couponProductFields' );

                add_action( 'woocommerce_payment_complete', __NAMESPACE__ . '\CouponAsProduct\PaymentComplete::completePayment' );

                add_action( 'woocommerce_after_order_notes', __NAMESPACE__ . '\CouponAsProduct\ExtraFields::addMOHOID' );
                add_action( 'woocommerce_checkout_process', __NAMESPACE__ . '\CouponAsProduct\ExtraFields::validateMOHOID');
                add_action( 'woocommerce_checkout_update_order_meta', __NAMESPACE__ . '\CouponAsProduct\ExtraFields::saveMOHOID' );

                add_filter( 'woocommerce_order_item_name', __NAMESPACE__ . '\CouponAsProduct\ReviewCart::itemAppendCouponCode', 10, 2);

            } else {
                add_action( 'admin_notices', __NAMESPACE__ . '\CouponAsProduct::WooCommerceNotActiveNotice' );
            }

            add_action( 'plugins_loaded', array( $this, 'loadTextdomain' ) );
            do_action('woocommerce_coupon_as_product_loaded');
        }

        /**
         * Main CouponAsProduct Instance
         *
         * Ensure only one instance of CouponAsProduct is loaded or can be loaded.
         *
         * @static
         * @see CouponAsProduct()
         * @return CouponAsProduct - Main Instance
         */
        public static function instance()
        {
            if( empty(self::$_instance) || !self::$_instance instanceof CouponAsProduct ) {
                self::$_instance = new CouponAsProduct();
            }

            return self::$_instance;
        }

        /**
         * Load Textdomain
         *
         * Load plugin's languages package.
         */

        public function loadTextdomain()
        {
            load_plugin_textdomain( 'woocommerce_coupon_as_product', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
        }

        /**
         * Check WooCommerce Active
         *
         * Check blog is active woocommerce or not
         *
         * @param int $blogID
         * @return bool
         */

        public static function isWooCommerceActive( $blogID = null )
        {
            $activePlugins = null;
            if($blogID && is_multisite()) {
                $activePlugins = get_blog_option($blogID, 'active_plugins');
            } else {
                $activePlugins = get_option('active_plugins');
            }

            if( !$activePlugins ) { return false; }
            return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins',  $activePlugins) );
        }

        /**
         * WooCommerce Not Active Notice
         *
         *  Display notice to alert user woocommerce not active.
         */

        public static function WooCommerceNotActiveNotice()
        {
            echo '<div class="updated fade">';
            echo '<p>' . __( 'You need active WooCommerce to make Coupon As Product working.', 'woocommerce_coupon_as_product' ) . '</p>';
            echo '</div>';
        }
    }
}

/**
 * Returns the main instance of CouponAsProduct to preent the need to use globals.
 *
 * @return CouponAsProduct
 */
function CouponAsProduct()
{
    return CouponAsProduct::instance();
}

CouponAsProduct();
