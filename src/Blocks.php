<?php

namespace Checkout\Contribuinte;

use WP_Post;
use Contribuinte_Checkout_Blocks_Integration;

class Blocks
{
    public function __construct()
    {
        $this->registerAddAttributesToBlock();
        $this->registerCheckoutEditorSave();
        $this->registerBlockIntegration();
        $this->registerCategory();
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
     * Save page hook
     *
     * @see https://gist.github.com/Bradley-D/7287723
     * @see https://www.billerickson.net/access-gutenberg-block-data/
     * @see https://developer.wordpress.org/reference/functions/parse_blocks/
     *
     * @return void
     */
    private function registerCheckoutEditorSave()
    {
        $callback = function ($postId, WP_Post $post) {
            if (!function_exists('parse_blocks')) {
                return; // WordPress version does not support this function
            }

            if (wp_is_post_revision($postId)) {
                return; // It's a revision, do not bother
            }

            $checkoutPageId = get_option('woocommerce_checkout_page_id');

            if ($postId !== $checkoutPageId) {
                return; // Page is not checkout
            }

            $allPageBlocks = parse_blocks(get_post($checkoutPageId)->post_content);

            if (empty($allPageBlocks)) {
                return; // no blocks found
            }

            $checkoutBlock = [];
            $checkoutFieldsBlock = [];
            $checkoutFieldsVatBlock = [];

            foreach ($allPageBlocks as $block) {
                if ('woocommerce/checkout' === $block['blockName']) {
                    $checkoutBlock = $block;
                    break;
                }
            }

            if (empty($checkoutBlock)) {
                return; // no checkout block found
            }

            foreach ($checkoutBlock['innerBlocks'] as $innerBlock) {
                if ('woocommerce/checkout-fields-block' === $innerBlock['blockName']) {
                    $checkoutFieldsBlock = $innerBlock;
                    break;
                }
            }

            if (empty($checkoutFieldsBlock)) {
                return; // no checkout fields block found
            }

            foreach ($checkoutFieldsBlock['innerBlocks'] as $innerBlock) {
                if ('contribuinte-checkout/checkout-block' === $innerBlock['blockName']) {
                    $checkoutFieldsVatBlock = $innerBlock;
                    break;
                }
            }

            if (empty($checkoutFieldsVatBlock)) {
                return; // no vat field block found
            }

            $data = $checkoutFieldsVatBlock['attrs'];

            foreach ($data as $key => $value) {

            }

            // todo: save settings data !!
        };

        add_action( 'save_post_page', $callback, 10, 2);
    }

    /**
     * Resgister checkout block
     *
     * @return void
     */
    private function registerBlockIntegration()
    {
        $callback = function () {
            require_once CONTRIBUINTE_CHECKOUT_DIR . '/contribuinte-checkout-blocks-integration.php';

            add_action(
                'woocommerce_blocks_checkout_block_registration',
                function ($integration_registry) {
                    $integration_registry->register(new Contribuinte_Checkout_Blocks_Integration());
                }
            );
        };

        add_action('woocommerce_blocks_loaded', $callback);
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
