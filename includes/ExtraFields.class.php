<?php
/**
 * Class ExtraFields
 * @author Zheng Xian Qiu
 */

namespace MOHO\CouponAsProduct;

class ExtraFields
{
    const MOHO_API_ENDPOINT = "https://magic-bonus.com/api/v3";

    public static function addMOHOID( $checkout )
    {
        echo '<div id="moho_information"><h2>' . __( 'MOHO Information', 'woocommerce_coupon_as_product' ) . '</h2>';

        woocommerce_form_field( 'moho_id', array(
            'type' => 'text',
            'class' => array('form-row-wide'),
            'label' => __('MOHO ID', 'woocommerce_coupon_as_product'),
            'placeholder' => __('MOHO ID', 'woocommerce_coupon_as_product'),
        ), $checkout->get_value( 'moho_id' ) );

        echo '</div>';
    }

    public static function validateMOHOID()
    {
        if( !$_POST['moho_id'] ) {
            wc_add_notice( __('Please fill your MOHO ID to make sure your can receive your bonus.', 'woocommerce_coupon_as_product'), 'error' );
        }
        if( !self::verifyMohoID($_POST['moho_id']) ) {
             wc_add_notice( __('This MOHO ID cannot be found, please check your MOHO ID.', 'woocommerce_coupon_as_product'), 'error' );
        }
    }

    public static function saveMOHOID( $orderID )
    {
        if( !empty($_POST['moho_id']) ) {
            update_post_meta( $orderID, 'moho_id', wc_clean( $_POST['moho_id'] ) );
        }
    }

    /**
     * Verify MOHO ID
     *
     * Check mono user id is exists or not
     *
     * @param int $mohoID
     * @return bool
     */
    public static function verifyMohoID( $mohoID )
    {
        $response = file_get_contents( self::MOHO_API_ENDPOINT . '/user/public/' . $mohoID );
        $data = json_decode($response);

        if( !isset($data->error) ) {
            return true;
        }

        return false;
    }
}

