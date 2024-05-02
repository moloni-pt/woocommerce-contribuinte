<?php

namespace Checkout\Contribuinte\Blocks;

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

class ExtendStoreEndpoint
{
    private $extend;

    /**
     * Plugin Identifier, unique to each plugin.
     *
     * @var string
     */
    const IDENTIFIER = 'contribuinte-checkout';

    public function init()
    {
        $this->extend = StoreApi::container()->get(ExtendSchema::class);
        $this->extend_store();
    }

    public function extend_store()
    {
        if (is_callable([$this->extend, 'register_endpoint_data'])) {
            $this->extend->register_endpoint_data(
                [
                    'endpoint' => CheckoutSchema::IDENTIFIER,
                    'namespace' => self::IDENTIFIER,
                    'schema_callback' => [$this, 'storeApiSchemaCallback'],
                    'schema_type' => ARRAY_A,
                ]
            );

            $this->extend->register_endpoint_data(
                [
                    'endpoint' => CartSchema::IDENTIFIER,
                    'namespace' => self::IDENTIFIER,
                    'schema_callback' => [$this, 'storeApiSchemaCallback'],
                    'data_callback' => [$this, 'storeApiDataCallback'],
                    'schema_type' => ARRAY_A,
                ]
            );
        }
    }

    public function storeApiDataCallback()
    {
        $data = wc()->session->get(self::IDENTIFIER);

        $billingVat = isset($data['billingVat']) ? $data['billingVat'] : null;

        if (null === $billingVat) {
            $customer = wc()->customer;

            if ($customer instanceof \WC_Customer) {
                $billingVat = $customer->get_meta('billing_vat');
            }
        }

        return [
            'billingVat' => empty($billingVat) ? '' : $billingVat
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
                    'validate_callback' => function( $value ) {
                        // todo: check errors here?
                        /*return new \WP_Error(
                            'rest_invalid_param',
                            'asdasdas'
                        );

                        return false;*/
                    },
                ],
            ],
        ];
    }
}
