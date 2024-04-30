import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState, useCallback } from '@wordpress/element';
import { getSetting } from "@woocommerce/settings";
import { FormStep } from '@woocommerce/blocks-components';
import { CHECKOUT_STORE_KEY, VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

const settings = getSetting('contribuinte-checkout_data', '');

const Block = (data) => {
    const {
        extensions,
        checkoutExtensionData,
        showStepNumber,
        sectionTitle,
        sectionDescription,
        inputLabel,
    } = data;
console.log(data);
    const validationErrorId = 'billing_vat';

    const { setValidationErrors, clearValidationError } = useDispatch(VALIDATION_STORE_KEY);
    const validationError = useSelect((select) => {
        const store = select('wc/store/validation');
        return store.getValidationError(validationErrorId);
    }, []);
    const { setExtensionData } = checkoutExtensionData;
    const checkoutIsProcessing = useSelect((select) =>
            select(CHECKOUT_STORE_KEY)?.isProcessing()
        , []);

    const [vatValue, setVatValue] = useState('');

    useEffect(() => {
        setExtensionData('contribuinte-checkout', 'billingVat', vatValue);

        // todo: do some verifications
    }, [setExtensionData, vatValue, setVatValue]);

    return (
        <FormStep
            id="fiscal-details"
            disabled={checkoutIsProcessing}
            title={__(sectionTitle || 'Fiscal details', 'contribuinte-checkout')}
            description={__(sectionDescription || '', 'contribuinte-checkout')}
            showStepNumber={!showStepNumber || showStepNumber === 'true'}
        >
            <ValidatedTextInput
                id="billing_vat"
                errorId="billing_vat"
                type="text"
                showError={true}
                value={vatValue}
                onChange={setVatValue}
                label={__(inputLabel || 'VAT', 'contribuinte-checkout')}
            />
        </FormStep>
    );
}

export default Block;
