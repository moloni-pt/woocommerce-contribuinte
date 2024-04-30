<?php

namespace Checkout\Contribuinte\Blocks;

class ExtendWooCore
{
    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    private $name = 'contribuinte-checkout';

    public function init()
    {
        $this->registerCheckoutHook();
        $this->registerBlockCategory();
        $this->registerAddAttributesToBlock();
    }

    private function registerCheckoutHook()
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

    /**
     * Register category
     *
     * @see https://developer.wordpress.org/reference/hooks/block_categories/
     * @see https://developer.wordpress.org/reference/hooks/block_categories_all/
     *
     * @return void
     */
    private function registerBlockCategory()
    {
        $callback = function ($block_categories) {
            $category = [
                'slug' => 'contribuinte-checkout-category',
                'title' => __('Contribuinte Checkout', 'contribuinte-checkout'),
                'icon' => null,
            ];

            if (is_array($block_categories)) {
                $existingSlugs = array_column($block_categories, 'slug');

                if (in_array($category['slug'], $existingSlugs)) {
                    return $block_categories;
                }
            }

            array_unshift($block_categories, $category);

            return $block_categories;
        };

        if (version_compare(get_bloginfo('version'), '5.8', '>=')) {
            $filter = 'block_categories_all';
        } else {
            $filter = 'block_categories';
        }

        add_filter($filter, $callback, 10, 1);
    }
}
