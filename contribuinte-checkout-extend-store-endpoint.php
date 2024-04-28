<?php

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

class Contribuinte_Checkout_Extend_Store_Endpoint
{
    private static $extend;

    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    const IDENTIFIER = 'contribuinte-checkout';

    public static function init()
    {
        self::$extend = StoreApi::container()->get(ExtendSchema::class);
        self::extend_store();
    }

    public static function extend_store()
    {
        if (is_callable([self::$extend, 'register_endpoint_data'])) {
            self::$extend->register_endpoint_data(
                [
                    'endpoint' => CartSchema::IDENTIFIER,
                    'namespace' => self::IDENTIFIER,
                    'schema_callback' => [self::class, 'store_api_schema_callback'],
                    'schema_type' => ARRAY_A,
                    'data_callback' => [self::class, 'store_api_data_callback'],
                ]
            );
        }

        if (is_callable([self::$extend, 'register_update_callback'])) {
            self::$extend->register_update_callback(
                [
                    'namespace' => self::IDENTIFIER,
                    'callback' => [self::class, 'store_api_update_callback'],
                ]
            );
        }
    }

    public static function store_api_update_callback($data)
    {
        if (!(isset(wc()->session) && wc()->session->has_session())) {
            wc()->session->set_customer_session_cookie(true);
        }

        wc()->session->set(self::IDENTIFIER, $data);
    }

    public static function extend_checkout_schema()
    {
        return array(
            'billingVat' => [
                'field' => 'data',
            ],
        );
    }

    public static function store_api_data_callback()
    {
        $data = array(
            'billingVat' => '',
            'isValid' => true,
        );

        return $data;
    }

    public static function store_api_schema_callback()
    {
        return array(
            'billingVat' => [
                'description' => __('VAT number', 'contribuinte-checkout'),
                'type' => 'string',
                'context' => ['view', 'edit'],
                'readonly' => true,
                'optional' => true,
            ],
        );
    }
}
