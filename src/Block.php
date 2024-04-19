<?php

namespace Checkout\Contribuinte;

use WP_Block_Editor_Context;

class Block
{
    public function __construct()
    {
        $this->registerBlock();
        $this->registerCategory();
    }

    public static function init()
    {
        return new (__CLASS__);
    }

    private function registerBlock()
    {
        $blocks = [
            'checkout-block',
        ];

        foreach ($blocks as $block) {
            register_block_type(CONTRIBUINTE_CHECKOUT_DIR . "/build/$block");
        }
    }

    private function registerCategory()
    {
        $callback = function ($block_categories) {
            $category = [
                'slug' => 'contribuinte-checkout-category',
                'title' => __('Contribuinte Checkout', 'contribuinte-checkout'),
                'icon' => 'contribuinte-checkout-icon',
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
