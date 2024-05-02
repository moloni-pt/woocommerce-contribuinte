import {__} from "@wordpress/i18n";

const tmpPhrases = {
    vat: __('VAT', 'contribuinte-checkout'),
    optional: __('(Optional)', 'contribuinte-checkout'),
    fiscalDetails: __('Fiscal details', 'contribuinte-checkout'),
    enterValidVat: __('Please enter a valid VAT number', 'contribuinte-checkout'),
    manageSettings: __('Manage field settings', 'contribuinte-checkout'),
    manageSettingsDescription: __('Options that control this section can be managed in the plugin settings page.', 'contribuinte-checkout'),
    behaviourOptions: __('Behaviour Options', 'contribuinte-checkout'),
    fieldLabel: __('Field label', 'contribuinte-checkout'),
    sectionDescription: __('Section description', 'contribuinte-checkout'),
    sectionTitle: __('Section title', 'contribuinte-checkout'),
    visualOptions: __('Visual Options', 'contribuinte-checkout'),
    optionalTextFormStep: __('Optional text for this form step.', 'woocommerce'),
    formStepOptions: __('Form Step Options', 'woocommerce'),
    showStepNumber: __('Show step number', 'woocommerce'),
}

export type PhrasesKeys = keyof typeof tmpPhrases;

export const phrases: Record<PhrasesKeys, string> = tmpPhrases;
