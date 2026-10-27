<?php

namespace Checkout\Contribuinte\Vies;

use SoapClient;

class Vies
{
    /**
     * Vies service URL
     * @var string
     */
    private $url = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * EU member states as VIES knows them (Greece is "EL", not "GR")
     *
     * @var string[]
     */
    private static $countries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'FI', 'FR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    /**
     * Country code
     *
     * @var string
     */
    private $countryCode;

    /**
     * Vat number
     *
     * @var string
     */
    private $vatNumber;

    /**
     * Vies constructor.
     * @param $countryCode
     * @param $vatNumber
     */
    public function __construct($countryCode, $vatNumber)
    {
        $this->countryCode = self::toViesCountry($countryCode);
        $this->vatNumber = $vatNumber;

        $this->validateVat();
    }

    /**
     * Checks VAT validity
     * @return array
     */
    public function checkVat()
    {
        // Pages call this on every view, so cache successful lookups
        $cacheKey = 'contribuinte_checkout_vies_' . md5($this->countryCode . $this->vatNumber);
        $result = get_transient($cacheKey);

        if ($result !== false) {
            return $result;
        }

        // VIES is often slow or down; don't hang the page for PHP's default 60s
        $previousTimeout = function_exists('ini_set') ? ini_set('default_socket_timeout', 5) : false;

        try {
            $client = new SoapClient($this->url, ['connection_timeout' => 5]);
            $result = $client->__soapCall('checkVat', [
                [
                    'countryCode' => $this->countryCode,
                    'vatNumber' => $this->vatNumber
                ]
            ]);

            set_transient($cacheKey, $result, DAY_IN_SECONDS);
        } catch (\SoapFault $exception) {
            //error validating VAT
            $result = $exception->getMessage();
        }

        if ($previousTimeout !== false) {
            ini_set('default_socket_timeout', $previousTimeout);
        }

        return $result;
    }

    /**
     * VIES only knows EU VAT numbers
     *
     * @param string $countryCode WooCommerce country code
     *
     * @return bool
     */
    public static function isSupportedCountry($countryCode)
    {
        return in_array(self::toViesCountry($countryCode), self::$countries, true);
    }

    //        Privates        //

    /**
     * WooCommerce uses ISO codes, VIES uses "EL" for Greece
     *
     * @param string $countryCode
     *
     * @return string
     */
    private static function toViesCountry($countryCode)
    {
        $countryCode = strtoupper((string)$countryCode);

        return $countryCode === 'GR' ? 'EL' : $countryCode;
    }

    /**
     * Removes unneeded prefix from VAT
     *
     * @return void
     */
    private function validateVat()
    {
        if (!empty($this->countryCode) && !empty($this->vatNumber)) {
            $codeLength = strlen($this->countryCode);
            $vatStart = substr($this->vatNumber, 0, $codeLength);

            if (strtoupper($vatStart) === strtoupper($this->countryCode)) {
                $this->vatNumber = substr($this->vatNumber, $codeLength);
            }
        }
    }

    //        Renders        //

    /**
     * Renders VIES information
     * @param $result
     */
    public function getViesForAdminOrderDataAfterBillingAddress($result)
    {
        //got error fetching data
        if (!isset($result->valid)) {
            ?>
            <div class="order_data_column" style="width: 100%;">
                <h4><?= __('VIES information', 'contribuinte-checkout') ?></h4>
                <p><?= __('Error fetching VIES  information', 'contribuinte-checkout') . ': ' . esc_html($result) ?></p>
            </div>
            <?php

            return;
        }

        //the vat is not from a company
        if ((int)$result->valid !== 1) {
            return;
        }

        //show fetched company data
        ?>
        <div class="order_data_column" style="width: 100%;">
            <h3><?= __('VIES information', 'contribuinte-checkout') ?></h3>
            <div class="vies-information">
                <p class="form-field form-field-wide"><?= esc_html($result->name) ?></p>
                <p class="form-field form-field-wide"><?= esc_html($result->address) ?></p>
                <p class="form-field form-field-wide"><?= esc_html(strtoupper($result->countryCode)) . esc_html($result->vatNumber) ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Renders VIES information
     * @param $result
     */
    public function getViesForAfterEditAccountAddressForm($result)
    {
        //got error fetching data
        if (!isset($result->valid)) {
            ?>
            <div class="order_data_column" style="width: 100%;">
                <h3><?= __('VIES information', 'contribuinte-checkout') ?></h3>
                <p><?= __('Error fetching VIES  information', 'contribuinte-checkout') . ': ' . esc_html($result) ?></p>
            </div>
            <?php

            return;
        }

        //the vat is not from a company
        if ((int)$result->valid !== 1) {
            return;
        }

        //Show fetched company data
        ?>
        <div class="vies-information">
            <h3><?= __('VIES information', 'contribuinte-checkout') ?></h3>
            <address>
                <?= esc_html($result->name) ?>
                <br>
                <?= esc_html($result->address) ?>
                <br>
                <?= esc_html(strtoupper($result->countryCode)) . esc_html($result->vatNumber) ?>
            </address>
        </div>
        <?php
    }
}
