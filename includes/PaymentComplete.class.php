<?php
/**
 * Class PaymentComplete
 * @author Zheng Xian Qiu
 */

namespace MOHO\CouponAsProduct;

class PaymentComplete
{
    public static function completePayment( $orderID )
    {
        $order = new \WC_Order( $orderID );
        $items = $order->get_items();

        $mohoID = get_post_meta( $orderID, 'moho_id', true);

        foreach( $items as $itemID => $item ) {
            $productID = wc_get_order_item_meta( $itemID, '_product_id');
            $couponCode = uniqid( 'moho_' . $mohoID . '_' );

            $storeID = get_post_meta( $productID, '_store_id', true );
            $couponAmount = get_post_meta( $productID, '_discount', true );
            $minimumAmount = get_post_meta( $productID, '_minimum_amount', true);
            $availablePeriod = get_post_meta( $productID, '_available_period', true);

            $options = array(
                'minimum_amount' => $minimumAmount,
                'exclude_sale_items' => 'yes',
                'expiry_date' => wc_clean( date( 'Y-m-d', time() + 86400 * $availablePeriod ) )
            );

            if( !$storeID ) { continue; }
            $coupon = self::createCoupon( $storeID, $mohoID, $couponCode, $couponAmount, 'percent', $options);
            if($coupon) {
                woocommerce_add_order_item_meta($itemID, '_coupon_code', $couponCode);
            }
        }
    }

    /**
     * Create Coupon
     *
     * Create coupon to specify store
     *
     * @param int $storeID
     * @param int $mohoID MOHO User ID
     * @param string $couponCode
     * @param int $amount
     * @param string $type
     * @param array $options
     * @return object|bool coupon post object
     */
    public static function createCoupon( $storeID, $mohoID, $couponCode, $amount = 10, $type = 'percent', $options = array() )
    {
        $coupon = null;

        switch_to_blog($storeID);

        $defaultOptions = array(
            'individual_use' => 'yes',
            'apply_before_tax' => 'yes',
            'free_shipping' => 'no'
        );
        $options = array_merge($defaultOptions, $options);

        $coupon = array(
            'post_title' => $couponCode ,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );

        $newCouponID = wp_insert_post( $coupon );

        update_post_meta( $newCouponID, 'moho_id', $mohoID );
        update_post_meta( $newCouponID, 'discount_type', $type );
        update_post_meta( $newCouponID, 'coupon_amount', $amount );

        foreach($options as $optionName => $optionValue) {
            update_post_meta( $newCouponID, $optionName, $optionValue);
        }

        $coupon = get_post( $newCouponID );

        restore_current_blog();

        return $coupon;
    }
}

