/**
 * Links of interest
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 * @see https://wordpress.github.io/gutenberg/?path=/docs/docs-introduction--page
 */

import classnames from 'classnames';
import { useBlockProps, PlainText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, ExternalLink } from '@wordpress/components';
import { ADMIN_URL } from '@woocommerce/settings';
import { Title } from '@woocommerce/blocks-components';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';
import { phrases } from '../common/translations'

const FormStepHead = ({ children }) => (
    <div className="wc-block-components-checkout-step__heading">
        <Title
            aria-hidden="true"
            className="wc-block-components-checkout-step__title"
            headingLevel="2"
        >
            {children}
        </Title>
    </div>
);

const FormStepBody = ({ description, content }) => (
    <div className="wc-block-components-checkout-step__container">
        <p className="wc-block-components-checkout-step__description">
            {description}
        </p>
        <div className="wc-block-components-checkout-step__content">
            {content}
            <div/>
        </div>
    </div>
);

export default function Edit({ attributes, setAttributes }) {
    const {
        showStepNumber,
        sectionTitle,
        sectionDescription,
        inputLabel,
    } = attributes;

    const blockProps = useBlockProps({
        className: classnames('wc-block-components-checkout-step', '', {
            'wc-block-components-checkout-step--with-step-number':
            showStepNumber,
        }),
    });

    const onChange = (name, value) => {
        setAttributes({ ...attributes, [name]: value });
    }

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={phrases.formStepOptions}>
                    <ToggleControl
                        checked={showStepNumber}
                        label={phrases.showStepNumber}
                        onChange={(value) => {
                            onChange('showStepNumber', value);
                        }}
                    />
                </PanelBody>
                <PanelBody title={phrases.visualOptions}>
                    <TextControl
                        label={phrases.sectionTitle}
                        value={sectionTitle}
                        onChange={(value) => {
                            onChange('sectionTitle', value);
                        }}
                    />
                    <TextControl
                        label={phrases.sectionDescription}
                        value={sectionDescription}
                        onChange={(value) => {
                            onChange('sectionDescription', value);
                        }}
                    />
                    <TextControl
                        label={phrases.fieldLabel}
                        value={inputLabel}
                        onChange={(value) => {
                            onChange('inputLabel', value);
                        }}
                    />
                </PanelBody>
                <PanelBody title={phrases.behaviourOptions}>
                    <p className="wc-block-checkout__controls-text">
                        {phrases.manageSettingsDescription}
                    </p>
                    <ExternalLink
                        href={`${ADMIN_URL}admin.php?page=contribuintecheckout`}
                    >
                        {phrases.manageSettings}
                    </ExternalLink>
                </PanelBody>
            </InspectorControls>
            <FormStepHead>
                <PlainText
                    className={''}
                    value={sectionTitle || ''}
                    onChange={(value) => {
                        onChange('sectionTitle', value);
                    }}
                    style={{ backgroundColor: 'transparent' }}
                />
            </FormStepHead>
            <FormStepBody
                description={
                    <PlainText
                        className={sectionDescription ? '' : 'wc-block-components-checkout-step__description-placeholder'}
                        value={sectionDescription || ''}
                        placeholder={phrases.optionalTextFormStep}
                        onChange={(value) => {
                            onChange('sectionDescription', value);
                        }}
                        style={{ backgroundColor: 'transparent' }}
                    />
                }
                content={
                    <ValidatedTextInput
                        type="text"
                        value={''}
                        label={inputLabel || ''}
                        disabled
                    />
                }
            />
        </div>
    );
}
