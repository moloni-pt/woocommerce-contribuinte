<?php

namespace Checkout\Contribuinte\Blocks;

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;

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
                    'schema_callback' => [$this, 'store_api_schema_callback'],
                    'schema_type' => ARRAY_A,
                ]
            );
        }
    }

    public function store_api_data_callback()
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

    public function store_api_schema_callback()
    {
        return [
            'billingVat' => [
                'description' => __('VAT number', 'contribuinte-checkout'),
                'type' => 'string',
                'context' => ['view', 'edit'],
                'readonly' => true,
                'optional' => true
            ],
        ];
    }
}
