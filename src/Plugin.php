<?php

namespace Checkout\Contribuinte;

use Exception;
use WP_Error;
use WC_Order;
use Checkout\Contribuinte\Vies\Vies;
use Checkout\Contribuinte\Menus\Admin;
use Checkout\Contribuinte\Helpers\Context;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

class Plugin
{
    /**
     * Settings options name
     * @var string
     */
    private $settingsOptionsName = 'contribuinte-checkout-options';

    private $injectValidationInFooter = false;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        $this->actions(); //Loads some needed classes
        $this->setHooks(); //Sets all needed hooks
    }

    /**
     * Starts this class
     * @return string
     */
    public static function init()
    {
        $class = __CLASS__;
        return new $class;
    }

    /**
     * Call the translations and admin classes
     */
    public function actions()
    {
        new Translations(); //Loads translations
        new Admin($this); //Add options page inside WordPress settings
    }

    /**
     * Sets all hooks
     */
    public function setHooks()
    {
        // Common filters needed
        add_filter('woocommerce_customer_meta_fields', [$this, 'woocommerceCustomerMetaFields']); // ADMIN: Add field to user edit page
        add_filter('woocommerce_ajax_get_customer_details', [$this, 'woocommerceAjaxGetCustomerDetails'], 10, 2); // ADMIN:Add field to ajax billing get_customer_details
        add_filter('woocommerce_api_order_response', [$this, 'woocommerceApiOrderResponse'], 11, 2); // ADMIN: Add field to order when requested via API
        add_filter('woocommerce_api_customer_response', [$this, 'woocommerceApiCustomerResponse'], 10, 2); // ADMIN: Add field to customer when requested via API
        add_filter('plugin_action_links_' . plugin_basename(CONTRIBUINTE_CHECKOUT_PLUGIN_FILE), [$this, 'addActionLinks']); // Show settings link in plugins list

        // Common actions needed
        add_action('before_woocommerce_init', [$this, 'beforeWoocommerceInit']); // CORE: Confirm HPOS compatibility
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'woocommerceAdminOrderDataAfterBillingAddress']); // ADMIN: Show  vies information on admin order page under billing address.
        add_action('woocommerce_after_edit_account_address_form', [$this, 'woocommerceAfterEditAccountAddressForm']); // FRONT END: Show VIES information under addresses in my account page

        if (Context::isCheckoutBlockActive()) {
            // Block actions needed
            add_action('woocommerce_init', [$this, 'woocommerceBlocksLoaded'], 100);
            add_action('woocommerce_set_additional_field_value', [$this, 'woocommerceSetAdditionalFieldValue'], 10, 4);
            add_action('woocommerce_blocks_validate_location_address_fields', [$this, 'woocommerceBlocksValidateLocationAddressFields'], 10, 3);

            // Block filters needed
            add_filter("woocommerce_get_default_value_for_contribuinte-checkout/billing_vat", [$this, 'woocommerceGetDefaultValueFor'], 10, 3);

            return;
        }

        // Legacy actions needed
        add_action('woocommerce_after_save_address_validation', [$this, 'woocommerceAfterSaveAddressValidation'], 10, 3); // FRONT END: Verify VAT if set in settings
        add_action('woocommerce_checkout_process', [$this, 'woocommerceCheckoutProcess']); // FRONT END: Verify VAT if set in settings
        add_action('wp_footer', [$this, 'wpFooter']); // GENERAL: Draw in footer

        // Legacy filters needed
        add_filter('woocommerce_admin_billing_fields', [$this, 'woocommerceAdminBillingFields']); // ADMIN: Add field to order page
        add_filter('woocommerce_order_get_formatted_billing_address', [$this, 'woocommerceOrderGetFormattedBillingAddress'], 10, 3); // Append vat field to billing address
        add_filter('woocommerce_billing_fields', [$this, 'woocommerceBillingFields'], 10, 1); // GENERAL: Add field to billing address fields
    }

    /**
     * Renders the settings page
     * This method will be called when opening WooCommerce Contribuinte page under the tab Options
     */
    public function settingsPage()
    {
        $settings = new Settings();
        $settings->renderPage();
    }

    /**
     * Show settings link in plugins list
     * @param $links
     * @return array
     */
    public function addActionLinks($links)
    {
        $links[] = '<a href="' . admin_url('admin.php?page=contribuintecheckout') . '">' . __('Settings', 'contribuinte-checkout') . '</a>';

        return $links;
    }

    /**
     * Validates the VAT number
     * Only validates Portuguese vat number
     * @param string $vat vat number
     * @return bool
     */
    public function validateVat($vat)
    {
        if (preg_match('/^[123456789]\d{8}$/', $vat)) {
            $sum = 0;

            for ($i = 0; $i < 9; $i++) {
                $sum += $vat[$i] * (10 - ($i + 1));
            }

            if ((int)$vat[8] === 0) {
                if (($sum % 11) !== 0) {
                    $sum += 10;
                }
            }

            if (($sum % 11) !== 0) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Add field to order page
     *
     * @param $billingFields
     *
     * @return array
     */
    public function woocommerceAdminBillingFields($billingFields)
    {
        global $post;

        $isLegacyOrderType = !empty($post) && ($post->post_type === 'shop_order' || $post->post_type === 'shop_subscription');

        if (Context::isNewOrdersSystemEnabled() || $isLegacyOrderType) {
            $settings = get_option($this->settingsOptionsName);

            if (!empty($settings) && isset($settings['text_box_vat_field_label']) && !empty($settings['text_box_vat_field_label'])) {
                $settingsLabel = $settings['text_box_vat_field_label'];
            } else {
                $settingsLabel = __('VAT', 'contribuinte-checkout');
            }

            if (empty($billingFields)) {
                $billingFields = [];
            }

            $billingFields['vat'] = [
                'label' => $settingsLabel
            ];
        }

        return $billingFields;
    }

    /**
     * Add field to user edit page
     * @param $profileFields
     * @return array
     */
    public function woocommerceCustomerMetaFields($profileFields)
    {
        $settings = get_option($this->settingsOptionsName);

        if (isset($profileFields['billing']) && is_array($profileFields['billing']['fields'])) {
            $profileFields['billing']['fields']['billing_vat'] = [
                'label' => empty($settings['text_box_vat_field_label']) ? __('VAT', 'contribuinte-checkout') : $settings['text_box_vat_field_label'],
                'description' => empty($settings['text_box_vat_field_description']) ? __('VAT Number', 'contribuinte-checkout') : $settings['text_box_vat_field_description']
            ];
        }
        return $profileFields;
    }

    /**
     * Add field to ajax billing get_customer_details
     * @param $data
     * @param $customer
     * @return array
     */
    public function woocommerceAjaxGetCustomerDetails($data, $customer)
    {
        if ((isset($data['billing']['country']))) {
            $data['billing']['vat'] = $customer->get_meta('_billing_vat');
        }

        return $data;
    }

    /**
     * Add field to order when requested via API
     * @param $orderData
     * @param $order
     * @return array
     */
    public function woocommerceApiOrderResponse($orderData, $order)
    {
        if (isset($orderData['billing_address'])) {
            $billingVat = $order->get_meta('_billing_vat');
            $orderData['billing_address']['vat'] = $billingVat;
        }

        return $orderData;
    }

    /**
     * Add field to customer when requested via API
     * @param $customerData
     * @param $customer
     * @return array
     */
    public function woocommerceApiCustomerResponse($customerData, $customer)
    {
        if (isset($customerData['billing_address'])) {
            $billingVat = $customer->get_meta('_billing_vat');
            $customerData['billing_address']['vat'] = $billingVat;
        }

        return $customerData;
    }

    /**
     * Append vat field to billing address
     *
     * @param string $address Formatted billing address
     * @param array $rawAddress Billing address data
     * @param WC_Order $order Woocommerce order class
     *
     * @return string
     */
    public function woocommerceOrderGetFormattedBillingAddress($address, $rawAddress, $order)
    {
        $vat = $order->get_meta('_billing_vat');
        $settings = get_option($this->settingsOptionsName);

        if (!empty($settings) && isset($settings['text_box_vat_field_label']) && !empty($settings['text_box_vat_field_label'])) {
            $settingsLabel = $settings['text_box_vat_field_label'];
        } else {
            $settingsLabel = __('VAT', 'contribuinte-checkout');
        }

        if (!empty($vat)) {
            if (empty($address)) {
                $address = '';
            }

            $address .= '<br>';
            $address .= $settingsLabel;
            $address .= ': ';
            $address .= $vat;
        }

        return $address;
    }

    /**
     * Set plugin as HPOS compatible
     *
     * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
     *
     * @return void
     */
    public function beforeWoocommerceInit()
    {
        if (class_exists(FeaturesUtil::class)) {
            FeaturesUtil::declare_compatibility('custom_order_tables', CONTRIBUINTE_CHECKOUT_PLUGIN_FILE, true);
        }
    }

    /**
     * Show VIES information under billing address in admin order page
     *
     * @param $order
     */
    public function woocommerceAdminOrderDataAfterBillingAddress($order)
    {
        $settings = get_option($this->settingsOptionsName);

        if (!empty($settings) && isset($settings['drop_down_show_vies'])) {
            $showVies = (bool)$settings['drop_down_show_vies'];
        } else {
            $showVies = false;
        }

        $vat = $order->get_meta('_billing_vat');
        $country = $order->get_billing_country();

        if ($showVies === false || empty($vat)) {
            return;
        }

        $vies = new Vies($country, $vat);
        $result = $vies->checkVat();

        $vies->getViesForAdminOrderDataAfterBillingAddress($result);
    }

    /**
     * Show VIES information under addresses in my account page
     */
    public function woocommerceAfterEditAccountAddressForm()
    {
        $vat = WC()->customer->get_meta('billing_vat');
        $country = WC()->customer->get_billing_country();
        $showVies = (bool)get_option($this->settingsOptionsName)['drop_down_show_vies'];

        if ($showVies === false || empty($vat)) {
            return;
        }

        $vies = new Vies($country, $vat);
        $result = $vies->checkVat();

        $vies->getViesForAfterEditAccountAddressForm($result);
    }

    /**
     * Draw in WordPress footer
     *
     * @return void
     */
    public function wpFooter()
    {
        if (!$this->injectValidationInFooter) {
            return;
        }

        if (!is_checkout()) {
            return;
        }

        ?>
        <script>
            if (jQuery) {
                jQuery(function ($) {
                    var contryCode = jQuery('select#billing_country');
                    var vatInput = jQuery('input#billing_vat');

                    if (!contryCode.length || !vatInput.length) {
                        return;
                    }

                    var wrapper = vatInput.closest('.form-row');

                    if (!wrapper.length) {
                        return;
                    }

                    function validateVatPT(number) {
                        if (number.length !== 9) {
                            return false;
                        }

                        if (!/^\d+$/.test(number)) {
                            return false;
                        }

                        var digits = number.split('').map(Number);
                        var sum = 0;

                        for (var i = 0; i < 8; i++) {
                            sum += digits[i] * (9 - i);
                        }

                        var rest = sum % 11;
                        var controlDigit = rest === 0 ? 0 : 11 - rest;

                        return controlDigit === digits[8];
                    }

                    function onInputChange() {
                        var value = vatInput.val().toString().trim();

                        if (contryCode.val() !== 'PT' || value === '') {
                            wrapper.removeClass('woocommerce-validated');

                            if (!wrapper.hasClass('validate-required')) {
                                wrapper.removeClass('woocommerce-invalid');
                            }

                            return;
                        }

                        if (validateVatPT(value)) {
                            wrapper
                                .removeClass('woocommerce-invalid')
                                .addClass('woocommerce-validated');
                        } else {
                            wrapper
                                .removeClass('woocommerce-validated')
                                .addClass('woocommerce-invalid');
                        }
                    }

                    vatInput.on('change validate', function () {
                        setTimeout(onInputChange, 100);
                    });
                });
            }
        </script>
        <?php
    }

    //      Legacy checkout      //

    /**
     * Add field to billing address fields
     *
     * @param array $fields
     * @return array
     */
    public function woocommerceBillingFields($fields)
    {
        list($label, $placeholder, $isRequired, $validateVat) = $this->getPropsForInput();

        if ($validateVat) {
            $this->injectValidationInFooter = true;
        }

        $fields['billing_vat'] = [
            'type' => 'text',
            'label' => $label,
            'placeholder' => $placeholder,
            'required' => $isRequired,
            'autocomplete' => 'on',
            'priority' => 120,
            'maxlength' => 20,
            'validate' => false,
            'class' => []
        ];

        return $fields;
    }

    /**
     * Verify VAT if set in settings adter saving the billing address
     * @param $userId
     * @param $loadAddress
     * @param $address
     */
    public function woocommerceAfterSaveAddressValidation($userId, $loadAddress, $address)
    {
        if ($loadAddress !== 'billing') {
            return;
        }

        if (isset($_POST['billing_vat'])) {
            $billingVAT = sanitize_text_field($_POST['billing_vat']);
        } elseif (isset($_POST['_wc_billing/contribuinte-checkout/billing_vat'])) {
            $billingVAT = sanitize_text_field($_POST['_wc_billing/contribuinte-checkout/billing_vat']);
        } else {
            $billingVAT = '';
        }

        $billingCountry = sanitize_text_field(isset($_POST['billing_country']) ? $_POST['billing_country'] : '');

        $this->runFormValidations($billingVAT, $billingCountry);
    }

    /**
     * Verify VAT if set in settings when order is in checkout
     */
    public function woocommerceCheckoutProcess()
    {
        $billingVAT = sanitize_text_field(isset($_POST['billing_vat']) ? $_POST['billing_vat'] : '');
        $billingCountry = sanitize_text_field(WC()->customer->get_billing_country());

        $this->runFormValidations($billingVAT, $billingCountry);
    }

    //      Blocks checkout      //

    public function woocommerceBlocksLoaded()
    {
        list($label, $placeholder, $isRequired) = $this->getPropsForInput();

        try {
            woocommerce_register_additional_checkout_field(
                [
                    'id' => 'contribuinte-checkout/billing_vat',
                    'label' => $label,
                    'location' => 'address',
                    'type' => 'text',
                    'group' => 'billing',
                    'required' => (bool)$isRequired,
                    "attributes" => [
                        "title" => $placeholder,
                    ],
                    'sanitize_callback' => function ($field_value) {
                        return trim($field_value);
                    },
                ]
            );
        } catch (Exception $e) {
            return;
        }
    }

    public function woocommerceBlocksValidateLocationAddressFields(WP_Error $errors, $fields, $group)
    {
        if ($group !== 'billing') {
            return;
        }

        if (!isset($fields['contribuinte-checkout/billing_vat'])) {
            return;
        }

        if (isset($fields['country'])) {
            $billingCountry = $fields['country'];
        } elseif (isset($_POST['billing_country'])) {
            $billingCountry = sanitize_text_field($_POST['billing_country']);
        } else {
            $billingCountry = '';
        }

        $billingVAT = $fields['contribuinte-checkout/billing_vat'];

        $this->runFormValidations($billingVAT, $billingCountry, $errors);
    }

    public function woocommerceSetAdditionalFieldValue($key, $value, $group, $wc_object)
    {
        if ('contribuinte-checkout/billing_vat' !== $key) {
            return;
        }

        if ($group !== 'billing') {
            return;
        }

        $wc_object->update_meta_data('_billing_vat', $value, true);
    }

    public function woocommerceGetDefaultValueFor($value, $group, $wc_object)
    {
        if (empty($value)) {
            return $wc_object->get_meta("_billing_vat");
        }

        return $value;
    }

    //      Auxiliary      //

    public function runFormValidations($billingVAT, $billingCountry, $errorObject = null)
    {
        $settings = get_option($this->settingsOptionsName);

        $validateVat = (bool)$settings['drop_down_validate_vat'];
        $isRequired = (bool)$settings['drop_down_is_required'];
        $isRequiredOverLimit = (bool)$settings['drop_down_required_over_limit_price'];
        $validationFail = (bool)$settings['drop_down_on_validation_fail'];

        if ($isRequiredOverLimit && is_checkout() && !empty(WC()->cart)) {
            $orderValue = WC()->cart->get_total('hook');

            if ($orderValue > 1000) {
                $isRequired = true;
            }
        }

        if (empty($billingVAT)) {
            if ($isRequired) {
                $message = __('A vat number is required.', 'contribuinte-checkout');

                $this->addFormError($message, "missing_billing_vat", $errorObject);
            }

            return;
        }

        if (!$validateVat) {
            return;
        }

        if ($billingCountry !== 'PT') {
            return;
        }

        if ($this->validateVat($billingVAT)) {
            return;
        }

        $message = __('You have entered an invalid VAT.', 'contribuinte-checkout');

        if ((int)$validationFail === 1) {
            wc_add_notice($message, 'notice');

            return;
        }

        $this->addFormError($message, "invalid_billing_vat", $errorObject);
    }

    public function addFormError($message, $code = '', $errorObject = null)
    {
        if ($errorObject instanceof WP_Error) {
            $errorObject->add($code, $message);
        } else {
            wc_add_notice($message, 'error');
        }
    }

    public function getPropsForInput()
    {
        $settings = get_option($this->settingsOptionsName);

        if (is_array($settings)) {
            $label = empty($settings['text_box_vat_field_label']) ? __('VAT', 'contribuinte-checkout') : $settings['text_box_vat_field_label'];
            $placeholder = empty($settings['text_box_vat_field_description']) ? __('VAT Number', 'contribuinte-checkout') : $settings['text_box_vat_field_description'];
            $isRequired = (int)$settings['drop_down_is_required'];
            $isRequiredOverLimit = (int)$settings['drop_down_required_over_limit_price'];
            $validateVat = (bool)$settings['drop_down_validate_vat'];
        } else {
            $label = __('VAT', 'contribuinte-checkout');
            $placeholder = __('VAT Number', 'contribuinte-checkout');
            $isRequired = 0;
            $isRequiredOverLimit = 0;
            $validateVat = false;
        }

        // If hook is called during checkout and is required over limit
        if ($isRequiredOverLimit > 0 && is_checkout() && !empty(WC()->cart)) {
            $orderValue = WC()->cart->get_total('hook');

            if ($orderValue > 1000) {
                $isRequired = 1;
            }
        }

        return [$label, $placeholder, $isRequired, $validateVat];
    }
}
