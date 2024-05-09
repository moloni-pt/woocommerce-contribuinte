import {phrases} from "../common/translations";
import {BlockEditAttributes} from "../common/interfaces";

export const defaultAttributes: BlockEditAttributes = {
    showStepNumber: true,
    sectionTitle: phrases.fiscalDetails,
    sectionDescription: '',
    inputLabel: phrases.vat,
};
