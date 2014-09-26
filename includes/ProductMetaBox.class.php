<?php
/**
 * Class PorductMetaBox
 * @author Zheng Xian Qiu
 */

namespace MOHO\CouponAsProduct;

class ProductMetaBox
{
    public static function couponProductFields()
    {
        echo '<div class="options_group show_if_virtual">';

        woocommerce_wp_text_input( array( 'id' => '_store_id', 'label' => __( 'Store ID', 'woocommerce_coupon_as_product' ), 'type' => 'number' ) );
        woocommerce_wp_text_input( array( 'id' => '_discount', 'label' => __( 'Discount', 'woocommerce_coupon_as_product' ), 'data_type' => 'decimal' ) );
        woocommerce_wp_text_input( array( 'id' => '_minimum_amount', 'label' => __( 'Minimum Amount', 'woocommerce_coupon_as_product' ), 'data_type' => 'price' ) );
        woocommerce_wp_text_input( array( 'id' => '_available_period', 'label' => __( 'Available Period', 'woocommerce_coupon_as_product' ), 'type' => 'number' ) );

        echo '</div>';
    }

    public static function saveCouponProduct( $postID )
    {
        $storeID = $_POST['_store_id'];
        if( !empty($storeID) ) {
            if( !\MOHO\CouponAsProduct::isWooCommerceActive($storeID) ) {
                $storeID = 0;
            }
            update_post_meta( $postID, '_store_id', esc_attr( $storeID ) );
        }
        $discount = $_POST['_discount'];
        if( !empty($discount) ) {
            update_post_meta( $postID, '_discount', esc_attr( $discount ) );
        }
        $minimumAmount = $_POST['_minimum_amount'];
        if( !empty($minimumAmount) ) {
            update_post_meta( $postID, '_minimum_amount', esc_attr( $minimumAmount ) );
        }
        $availablePeriod = $_POST['_available_period'];
        if( !empty($availablePeriod) ) {
            update_post_meta( $postID, '_available_period', esc_attr( $availablePeriod ) );
        }
    }
}

