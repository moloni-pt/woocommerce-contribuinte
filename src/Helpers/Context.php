<?php

namespace Checkout\Contribuinte\Helpers;

use Automattic\WooCommerce\Utilities\OrderUtil;
use WC_Blocks_Utils;

class Context
{
    public static function isNewOrdersSystemEnabled()
    {
        if (class_exists(OrderUtil::class)) {
            return OrderUtil::custom_orders_table_usage_is_enabled();
        }

        return false;
    }

    public static function isCheckoutBlockActive()
    {
        if (class_exists(WC_Blocks_Utils::class)) {
            return WC_Blocks_Utils::has_block_in_page(wc_get_page_id('checkout'), 'woocommerce/checkout');
        }

        return false;
    }
}
