<?php
/**
 *
 *   Plugin Name:  Contribuinte Checkout
 *   Description:  Add VAT information to your orders
 *   Version:      1.0.50
 *   Tested up to: 6.2.0
 *   WC tested up to: 7.5.1
 *
 *   Author:       moloni.pt
 *   Author URI:   https://moloni.pt
 *   License:      GPL2
 *   License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *   Text Domain:  contribuinte-checkout
 *   Domain Path:  /languages
 */

namespace Checkout\Contribuinte;


use Contribuinte_Checkout_Blocks_Integration;
use Contribuinte_Checkout_Extend_Store_Endpoint;
use Contribuinte_Checkout_Extend_Woo_Core;

//Deny direct access
if (!defined('ABSPATH')) {
    exit;
}

//Starts autoloader, gets namespaces
$composer_autoloader = __DIR__ . '/vendor/autoload.php';
if (is_readable($composer_autoloader)) {
    /** @noinspection PhpIncludeInspection */
    require $composer_autoloader;
}

if (!defined('CONTRIBUINTE_CHECKOUT_VERSION')) {
    define( 'CONTRIBUINTE_CHECKOUT_VERSION', '2.0.0' );
}

if (!defined('CONTRIBUINTE_CHECKOUT_PLUGIN_FILE')) {
    define('CONTRIBUINTE_CHECKOUT_PLUGIN_FILE', __FILE__);
}

if (!defined('CONTRIBUINTE_CHECKOUT_DIR')) {
    define('CONTRIBUINTE_CHECKOUT_DIR', __DIR__);
}

// Register installation hook to run Install class static method (run())
register_activation_hook(__FILE__, 'Checkout\Contribuinte\Activators\Install::run');

// Start this plugin
add_action('plugins_loaded', 'Checkout\Contribuinte\Plugin::init');


$callback = function () {
    require_once CONTRIBUINTE_CHECKOUT_DIR . '/contribuinte-checkout-extend-store-endpoint.php';
    require_once CONTRIBUINTE_CHECKOUT_DIR . '/contribuinte-checkout-extend-woo-core.php';
    require_once CONTRIBUINTE_CHECKOUT_DIR . '/contribuinte-checkout-blocks-integration.php';

    Contribuinte_Checkout_Extend_Store_Endpoint::init();

    // Add hooks relevant to extending the Woo core experience.
    $extend_core = new Contribuinte_Checkout_Extend_Woo_Core();
    $extend_core->init();

    add_action(
        'woocommerce_blocks_checkout_block_registration',
        function ($integration_registry) {
            $integration_registry->register(new Contribuinte_Checkout_Blocks_Integration());
        }
    );
};

add_action('woocommerce_blocks_loaded', $callback);

$callback2 = function ($block_categories) {
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

add_filter($filter, $callback2, 10, 1);
