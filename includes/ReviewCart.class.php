<?php
/**
 * Class ReviewCart
 * @author Zheng Xian Qiu
 */

namespace MOHO\CouponAsProduct;

class ReviewCart
{
    public static function itemAppendCouponCode( $itemLink, $item )
    {
        if( isset($item['coupon_code']) ) {
            $itemLink .= ' (' . $item['coupon_code'] . ')';
        }
        return $itemLink;
    }
}

