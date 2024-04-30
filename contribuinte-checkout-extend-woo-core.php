<?php

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

class Contribuinte_Checkout_Extend_Woo_Core
{
    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    private $name = 'contribuinte-checkout';

    public function init()
    {
        $this->save_vat_field();
        $this->registerAddAttributesToBlock();
    }

    private function save_vat_field()
    {
        $callback = function (\WC_Order $order, \WP_REST_Request $request) {
            $thisData = $request['extensions'][$this->name];

            $vatValue = isset($thisData['billingVat']) ? $thisData['billingVat'] : '';

            $order->update_meta_data('_billing_vat', $vatValue);
            $order->save();
        };

        add_action('woocommerce_store_api_checkout_update_order_from_request', $callback, 10, 2);
    }

    /**
     * Append attributes to what’s going to be rendered
     *
     * @see https://developer.woocommerce.com/2021/11/15/how-does-woocommerce-blocks-render-interactive-blocks-in-the-frontend/
     *
     * @return void
     */
    private function registerAddAttributesToBlock()
    {
        $callback = function ($whitelisted_blocks) {
            $whitelisted_blocks[] = 'contribuinte-checkout/checkout-block';

            return $whitelisted_blocks;
        };

        add_action('__experimental_woocommerce_blocks_add_data_attributes_to_block', $callback);
    }
}
