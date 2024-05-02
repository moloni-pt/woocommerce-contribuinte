import { useSelect } from '@wordpress/data';
import { useEffect, useMemo, useState, useCallback } from '@wordpress/element';
import { getSetting } from "@woocommerce/settings";
import { FormStep } from '@woocommerce/blocks-components';
import { CART_STORE_KEY, CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';
import { validateVatPT } from '../common/helpers/validations';
import { phrases } from '../common/translations'

const {
    drop_down_is_required: shouldRequireVat,
    drop_down_required_over_limit_price: shouldRequireVatOverLimit,
    drop_down_validate_vat: shouldValidateVat,
    drop_down_on_validation_fail: shouldOnlyShowWarningOnFail
} = getSetting('contribuinte-checkout_data', '');

const Block = (data) => {
    const {
        extensions,
        checkoutExtensionData,
        showStepNumber,
        sectionTitle,
        sectionDescription,
        inputLabel,
    } = data;

    const { setExtensionData } = checkoutExtensionData;

    const checkoutIsProcessing = useSelect((select) => {
        return select(CHECKOUT_STORE_KEY)?.isProcessing();
    }, []);
    const checkoutTotals = useSelect((select) => {
        return select(CART_STORE_KEY)?.getCartTotals();
    }, []);
    const checkoutAddresses = useSelect((select) => {
        return select(CART_STORE_KEY)?.getCustomerData();
    }, []);

    const initialVatValue = useMemo(() => {
        try {
            return extensions['contribuinte-checkout']['billingVat'] || '';
        } catch (exception) {
            return '';
        }
    }, []);

    const [vatValue, setVatValue] = useState(initialVatValue);

    const isVatRequired = useMemo(() => {
        if (shouldRequireVat) {
            return true;
        }

        if (!shouldRequireVatOverLimit) {
            return false;
        }

        let total = 0;

        try {
            total = checkoutTotals?.total_price || 0;
        } catch (exception) {
        }

        return total > 100000;
    }, [checkoutTotals]);
    const isVatValid = useMemo(() => {
        let countryCode = '';

        try {
            countryCode = checkoutAddresses?.billingAddress?.country || '';
        } catch (exception) {
        }

        let valueToTest = vatValue.toString().trim()

        if (countryCode === 'PT') {
            return validateVatPT(valueToTest);
        }

        return true;
    }, [vatValue, checkoutAddresses]);
    const validateVatCallback = useCallback((inputObject) => {
        if (!shouldValidateVat || isVatValid || shouldOnlyShowWarningOnFail) {
            return true;
        }

        inputObject.setCustomValidity(phrases.enterValidVat);

        return false;
    }, [isVatValid]);

    useEffect(() => {
        setExtensionData('contribuinte-checkout', 'billingVat', vatValue);
    }, [
        vatValue,
        setExtensionData,
    ]);

    return (
        <FormStep
            id="fiscal-details"
            disabled={checkoutIsProcessing}
            title={
                sectionTitle && sectionTitle !== '' ? sectionTitle : phrases.fiscalDetails
            }
            description={
                sectionDescription ? sectionDescription : ''
            }
            showStepNumber={!showStepNumber || showStepNumber === 'true'}
        >
            <ValidatedTextInput
                id="billing_vat"
                errorId="billing_vat"
                type="text"
                value={vatValue}
                showError={true}
                validateOnMount={true}
                onChange={setVatValue}
                customValidation={validateVatCallback}
                required={isVatRequired}
                errorMessage={
                    shouldOnlyShowWarningOnFail && !isVatValid && vatValue !== '' ? phrases.enterValidVat : ''
                }
                label={
                    `${inputLabel && inputLabel !== '' ? inputLabel : phrases.vat} ${isVatRequired ? '' : phrases.optional}`
                }
            />
        </FormStep>
    );
}

export default Block;
