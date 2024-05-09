import {__} from "@wordpress/i18n";

const tmpPhrases = {
    debug: __('Debug', 'contribuinte-checkout')
}

export type PhrasesKeys = keyof typeof tmpPhrases;

export const phrases: Record<PhrasesKeys, string> = tmpPhrases;
