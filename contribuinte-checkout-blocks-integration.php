<?php

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks
 */
class Contribuinte_Checkout_Blocks_Integration implements IntegrationInterface
{

    /**
     * The name of the integration.
     *
     * @return string
     */
    public function get_name()
    {
        return 'contribuinte-checkout';
    }

    /**
     * When called invokes any initialization/setup for the integration.
     */
    public function initialize()
    {
        $this->register_block_frontend_scripts();
        $this->register_block_editor_scripts();
        $this->register_main_integration();

        $this->extend_store_api();
    }

    private function extend_store_api()
    {
        require_once __DIR__ . '/contribuinte-checkout-extend-store-endpoint.php';

        Contribuinte_Checkout_Extend_Store_Endpoint::init();
    }


    public function get_script_handles()
    {
        return ['contribuinte-checkout-blocks-integration', 'contribuinte-checkout-block-frontend'];
    }

    public function get_editor_script_handles()
    {
        return ['contribuinte-checkout-blocks-integration', 'contribuinte-checkout-block-editor'];
    }

    public function get_script_data()
    {
        $settings = get_option('contribuinte-checkout-options');

        if (empty($settings)) {
            $settings = [];
        }

        return [
            'drop_down_is_required' => isset($settings['drop_down_is_required']) ? $settings['drop_down_is_required']: '',
            'drop_down_required_over_limit_price' => isset($settings['drop_down_required_over_limit_price']) ? $settings['drop_down_required_over_limit_price']: '',
            'drop_down_validate_vat' => isset($settings['drop_down_validate_vat']) ? $settings['drop_down_validate_vat']: '',
            'drop_down_on_validation_fail' => isset($settings['drop_down_on_validation_fail']) ? $settings['drop_down_on_validation_fail']: '',
        ];
    }


    private function register_main_integration()
    {
        $script_path = '/build/index.js';
        $script_url = plugins_url($script_path, CONTRIBUINTE_CHECKOUT_PLUGIN_FILE);
        $script_asset_path = dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/build/index.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : [
                'dependencies' => [],
                'version' => $this->get_file_version($script_path),
            ];

        wp_register_script(
            'contribuinte-checkout-blocks-integration',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        wp_set_script_translations(
            'contribuinte-checkout-blocks-integration',
            'contribuinte-checkout',
            dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/languages'
        );
    }

    public function register_block_editor_scripts()
    {
        $script_path = '/build/contribuinte-checkout-block.js';
        $script_url = plugins_url($script_path, CONTRIBUINTE_CHECKOUT_PLUGIN_FILE);
        $script_asset_path = dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/build/contribuinte-checkout-block.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : [
                'dependencies' => [],
                'version' => $this->get_file_version($script_asset_path),
            ];

        wp_register_script(
            'contribuinte-checkout-block-editor',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );

        wp_set_script_translations(
            'contribuinte-checkout-block-editor',
            'contribuinte-checkout',
            dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/languages'
        );
    }

    public function register_block_frontend_scripts()
    {
        $script_path = '/build/contribuinte-checkout-block-frontend.js';
        $script_url = plugins_url($script_path, CONTRIBUINTE_CHECKOUT_PLUGIN_FILE);
        $script_asset_path = dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/build/contribuinte-checkout-block-frontend.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : [
                'dependencies' => [],
                'version' => $this->get_file_version($script_asset_path),
            ];

        wp_register_script(
            'contribuinte-checkout-block-frontend',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        wp_set_script_translations(
            'contribuinte-checkout-block-frontend',
            'contribuinte-checkout',
            dirname(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE) . '/languages'
        );
    }


    protected function get_file_version($file)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists($file)) {
            return filemtime($file);
        }

        return CONTRIBUINTE_CHECKOUT_VERSION;
    }
}
