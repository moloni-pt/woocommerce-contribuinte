<?php

namespace Checkout\Contribuinte\Blocks;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

class ExtendStoreEndpoint
{

    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    private $identifier = 'contribuinte-checkout';

    public function init()
    {
        $this->extend_store();
    }

    public function extend_store()
    {
        woocommerce_store_api_register_endpoint_data(
            [
                'endpoint' => CheckoutSchema::IDENTIFIER,
                'namespace' => $this->identifier,
                'schema_callback' => [$this, 'storeApiSchemaCallback'],
                'schema_type' => ARRAY_A,
            ]
        );

        woocommerce_store_api_register_endpoint_data(
            [
                'endpoint' => CartSchema::IDENTIFIER,
                'namespace' => $this->identifier,
                'schema_callback' => [$this, 'storeApiSchemaCallback'],
                'data_callback' => [$this, 'storeApiDataCallback'],
                'schema_type' => ARRAY_A,
            ]
        );
    }

    public function storeApiDataCallback()
    {
        $customer = wc()->customer;

        if ($customer instanceof \WC_Customer) {
            $billingVat = $customer->get_meta('billing_vat');
        }

        return [
            'billingVat' => empty($billingVat) ? '' : $billingVat,
        ];
    }

    public function storeApiSchemaCallback()
    {
        return [
            'billingVat' => [
                'description' => __('VAT number', 'contribuinte-checkout'),
                'type' => 'string',
                'context' => ['view', 'edit'],
                'readonly' => true,
                'optional' => true,
                'arg_options' => [
                    'validate_callback' => function ($value) {
                        return true;
                    },
                ],
            ],
        ];
    }
}
