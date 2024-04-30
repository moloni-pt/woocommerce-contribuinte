<?php

namespace Checkout\Contribuinte;

use Contribuinte_Checkout_Extend_Store_Endpoint;
use Contribuinte_Checkout_Blocks_Integration;

class Blocks
{
    public function __construct()
    {
        $this->registerCategory();
        $this->registerBlockIntegration();
        $this->registerAddAttributesToBlock();
    }

    private function extend_store_api()
    {
        Contribuinte_Checkout_Extend_Store_Endpoint::init();
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
     * Resgister checkout block
     *
     * @return void
     */
    private function registerBlockIntegration()
    {

    }

    /**
     * Register category
     *
     * @see https://developer.wordpress.org/reference/hooks/block_categories/
     * @see https://developer.wordpress.org/reference/hooks/block_categories_all/
     *
     * @return void
     */
    private function registerCategory()
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
