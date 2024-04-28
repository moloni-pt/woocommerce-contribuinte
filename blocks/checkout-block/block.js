import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState, useCallback } from '@wordpress/element';
import { getSetting } from "@woocommerce/settings";
import { FormStep } from '@woocommerce/blocks-components';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

const data = getSetting('contribuinte-checkout_data', '');

const Block = (fabio) => {
    console.log(fabio);

    const { extensions, cart, checkoutExtensionData, validation } = fabio;

    const { setExtensionData } = checkoutExtensionData;

    const checkoutIsProcessing = useSelect((select) =>
        select(CHECKOUT_STORE_KEY).isProcessing()
    );

    const debouncedSetExtensionData = useCallback(
        (namespace, key, value) => {
            setExtensionData(namespace, key, value);
        },
        [setExtensionData]
    );

    useEffect(() => {
        console.log('oii');
    }, []);

    return (
        <FormStep
            id="fiscal-details"
            disabled={checkoutIsProcessing}
            title={'TEst'}
            description={'ssss'}
            showStepNumber={true}
        >
            <ValidatedTextInput
                id="billing_vat"
                type="text"
                value={''}
                label={'asd'}
            />
        </FormStep>
    );
}

export default Block;
