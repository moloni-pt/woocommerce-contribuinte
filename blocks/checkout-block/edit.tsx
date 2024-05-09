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
import {useBlockProps, PlainText, InspectorControls} from '@wordpress/block-editor';
import {useEffect} from '@wordpress/element';
import {PanelBody, ToggleControl, TextControl, ExternalLink} from '@wordpress/components';
import {ADMIN_URL} from '@woocommerce/settings';
import {Title} from '@woocommerce/blocks-components';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import React from "react";
import type {FC, ReactNode} from 'react';
import {BlockEditAttributes} from "../common/interfaces";
import type {BlockEditProps} from "@wordpress/blocks";
import {__} from "@wordpress/i18n";
import {defaultAttributes} from "./attributes";
import {phrases} from "../common/translations";

const FormStepHead: FC<{ children: ReactNode }> = ({children}) => (
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

const FormStepBody: FC<{ description: ReactNode, content: ReactNode }> = ({description, content}) => (
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

const Edit: FC<BlockEditProps<BlockEditAttributes>> = ({attributes, setAttributes}) => {
    const {
        showStepNumber,
        sectionTitle,
        sectionDescription,
        inputLabel,
    } = attributes;

    useEffect(() => {
        if (Object.keys(attributes).length === 0) {
            setAttributes(defaultAttributes);
        }
    }, []);

    const blockProps = useBlockProps({
        className: classnames('wc-block-components-checkout-step', '', {
            'wc-block-components-checkout-step--with-step-number':
            showStepNumber,
        }),
    });

    const onChange = (name: string, value: string | number | boolean) => {
        setAttributes({...attributes, [name]: value});
    }

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Form Step Options', 'woocommerce')}>
                    <ToggleControl
                        checked={showStepNumber}
                        label={__('Show step number', 'woocommerce')}
                        onChange={(value: boolean) => {
                            onChange('showStepNumber', value);
                        }}
                    />
                </PanelBody>
                <PanelBody title={phrases.visualOptions}>
                    <TextControl
                        label={phrases.sectionTitle}
                        value={sectionTitle || ''}
                        onChange={(value: string) => {
                            onChange('sectionTitle', value);
                        }}
                    />
                    <TextControl
                        label={phrases.sectionDescription}
                        value={sectionDescription || ''}
                        onChange={(value: string) => {
                            onChange('sectionDescription', value);
                        }}
                    />
                    <TextControl
                        label={phrases.fieldLabel}
                        value={inputLabel || ''}
                        onChange={(value: string) => {
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
                    onChange={(value: string) => {
                        onChange('sectionTitle', value);
                    }}
                    style={{backgroundColor: 'transparent'}}
                />
            </FormStepHead>
            <FormStepBody
                description={
                    <PlainText
                        className={sectionDescription ? '' : 'wc-block-components-checkout-step__description-placeholder'}
                        value={sectionDescription || ''}
                        placeholder={__('Optional text for this form step.', 'woocommerce')}
                        onChange={(value: string) => {
                            onChange('sectionDescription', value);
                        }}
                        style={{backgroundColor: 'transparent'}}
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

export default Edit;
