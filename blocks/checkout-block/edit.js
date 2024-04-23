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
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, SelectControl } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';
import { FormStep } from '@woocommerce/blocks-components';


import './editor.scss';

const data = getSetting('contribuinte-checkout_data', '');


export default function Edit({attributes, setAttributes}) {
	const {
		text_box_vat_field_label,
		text_box_vat_field_description,
		drop_down_show_step_number,
		drop_down_is_required,
		drop_down_required_over_limit_price,
		drop_down_validate_vat,
		drop_down_on_validation_fail,
	} = attributes;
	const blockProps = useBlockProps();

	const onChange = (name, value) => {
		setAttributes({ ...attributes, [name]: value });
	}

	console.log(data);

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={__('Show step number', 'contribuinte-checkout')}>
					<ToggleControl
						checked={drop_down_show_step_number === true}
						label={__('Show step number', 'contribuinte-checkout')}
						onChange={(value) => {
							onChange('drop_down_show_step_number', value);
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
			<FormStep {...props}>
				<RichText
					value={text || optInDefaultText}
					onChange={(value) => setAttributes({ text: value })}
				/>
			</FormStep>
		</div>
	);
}
