/**
 * Links of interest
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 * @see https://wordpress.github.io/gutenberg/?path=/docs/docs-introduction--page
 */

import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { useBlockProps, PlainText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, SelectControl } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';
import { Title } from '@woocommerce/blocks-components';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

const data = getSetting('contribuinte-checkout_data', '');

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
        text_box_vat_field_label,
        text_box_vat_field_description,
        drop_down_is_required,
        drop_down_required_over_limit_price,
        drop_down_validate_vat,
        drop_down_on_validation_fail
    } = data;
    const {
        showStepNumber,
        title,
        description,
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
                <PanelBody title={__('Form Step Options', 'woocommerce')}>
                    <ToggleControl
                        checked={showStepNumber}
                        label={__('Show step number', 'woocommerce')}
                        onChange={(value) => {
                            onChange('showStepNumber', value);
                        }}
                    />
                </PanelBody>
                <PanelBody title={__('Visual', 'contribuinte-checkout')}>
                    <TextControl
                        label={__('VAT field label', 'contribuinte-checkout')}
                        value={text_box_vat_field_label}
                        onChange={(value) => {
                            onChange('text_box_vat_field_label', value);
                        }}
                    />
                    <TextControl
                        label={__('VAT field description', 'contribuinte-checkout')}
                        value={text_box_vat_field_description}
                        onChange={(value) => {
                            onChange('text_box_vat_field_description', value);
                        }}
                    />
                </PanelBody>
                <PanelBody title={__('Behaviour', 'contribuinte-checkout')}>
                    <ToggleControl
                        checked={drop_down_is_required === true}
                        label={__('VAT is required', 'contribuinte-checkout')}
                        onChange={(value) => {
                            onChange('drop_down_is_required', value);
                        }}
                    />
                    <ToggleControl
                        checked={drop_down_required_over_limit_price === true}
                        label={__('VAT required on orders over 1000€', 'contribuinte-checkout')}
                        onChange={(value) => {
                            onChange('drop_down_required_over_limit_price', value);
                        }}
                    />
                    <ToggleControl
                        checked={drop_down_validate_vat === true}
                        label={__('Validate VAT', 'contribuinte-checkout')}
                        onChange={(value) => {
                            onChange('drop_down_validate_vat', value);
                        }}
                    />
                    <SelectControl
                        label={__('Failed validation handling', 'contribuinte-checkout')}
                        value={drop_down_on_validation_fail}
                        onChange={(value) => {
                            onChange('drop_down_on_validation_fail', value);
                        }}
                        options={[
                            {
                                label: __('Reject the order and show customer an error', 'contribuinte-checkout'),
                                value: 0
                            },
                            {
                                label: __('Only show customer an warning message', 'contribuinte-checkout'),
                                value: 1
                            }
                        ]}
                    />
                </PanelBody>
            </InspectorControls>
            <FormStepHead>
                <PlainText
                    className={''}
                    value={title}
                    onChange={(value) => {
                        onChange('title', value);
                    }}
                    style={{ backgroundColor: 'transparent' }}
                />
            </FormStepHead>
            <FormStepBody
                description={
                    <PlainText
                        className={description ? '' : 'wc-block-components-checkout-step__description-placeholder'}
                        value={description || ''}
                        placeholder={__('Optional text for this form step.', 'woocommerce')}
                        onChange={(value) => {
                            onChange('description', value);
                        }}
                        style={{ backgroundColor: 'transparent' }}
                    />
                }
                content={
                    <ValidatedTextInput
                        type="text"
                        value={''}
                        label={text_box_vat_field_label || ''}
                        disabled
                    />
                }
            />
        </div>
    );
}
