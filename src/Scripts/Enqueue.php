<?php

namespace Checkout\Contribuinte\Scripts;

class Enqueue
{
    public static function defines()
    {
        if (wp_doing_ajax()) {
            return;
        }

        if (!isset($_REQUEST['page'])) {
            return;
        }

        if (sanitize_text_field($_REQUEST['page']) === 'contribuintecheckout') {
            wp_enqueue_script('checkout-settings', plugins_url('assets/js/Checkout.Settings.js', CONTRIBUINTE_CHECKOUT_PLUGIN_FILE));
            wp_enqueue_script('select2', plugins_url('assets/external/select2.full.min.js', CONTRIBUINTE_CHECKOUT_PLUGIN_FILE));
            wp_enqueue_style('select2', plugins_url('assets/external/select2.min.css', CONTRIBUINTE_CHECKOUT_PLUGIN_FILE));
        }
    }
}
