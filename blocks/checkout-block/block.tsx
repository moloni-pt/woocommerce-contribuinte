import {useSelect} from '@wordpress/data';
import {useEffect, useMemo, useState, useCallback} from '@wordpress/element';
import {getSetting} from "@woocommerce/settings";
import {FormStep} from '@woocommerce/blocks-components';
import {CART_STORE_KEY, CHECKOUT_STORE_KEY} from '@woocommerce/block-data';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import {validateVatPT} from '../common/helpers/validations';
import React, {FC} from 'react';
import {BlockViewAttributes} from "../common/interfaces";
import {__} from "@wordpress/i18n";

const {
    drop_down_is_required: shouldRequireVat,
    drop_down_required_over_limit_price: shouldRequireVatOverLimit,
    drop_down_validate_vat: shouldValidateVat,
    drop_down_on_validation_fail: shouldOnlyShowWarningOnFail
} = getSetting('contribuinte-checkout_data', '');

const Block: FC<BlockViewAttributes> = (data) => {
    const {
        extensions,
        checkoutExtensionData,
        showStepNumber,
        sectionTitle,
        sectionDescription,
        inputLabel,
    } = data;

    const {setExtensionData} = checkoutExtensionData;

    const checkoutIsProcessing = useSelect((select) => {
        // @ts-ignore
        return select(CHECKOUT_STORE_KEY)?.isProcessing();
    }, []);
    const checkoutTotals = useSelect((select) => {
        // @ts-ignore
        return select(CART_STORE_KEY)?.getCartTotals();
    }, []);
    const checkoutAddresses = useSelect((select) => {
        // @ts-ignore
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
    const validateVatCallback = useCallback((inputObject: HTMLInputElement) => {
        if (!shouldValidateVat || isVatValid || shouldOnlyShowWarningOnFail) {
            return true;
        }

        inputObject.setCustomValidity(__('Please enter a valid VAT number', 'contribuinte-checkout'));

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
                sectionTitle && sectionTitle !== '' ? sectionTitle : __('Fiscal details', 'contribuinte-checkout')
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
                    shouldOnlyShowWarningOnFail && !isVatValid && vatValue !== '' ? __('Please enter a valid VAT number', 'contribuinte-checkout') : ''
                }
                label={
                    `${inputLabel && inputLabel !== '' ? inputLabel : __('VAT', 'contribuinte-checkout')} ${isVatRequired ? '' : __('(Optional)', 'contribuinte-checkout')}`
                }
            />
        </FormStep>
    );
}

export default Block;
