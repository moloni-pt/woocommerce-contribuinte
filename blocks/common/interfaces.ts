export interface BlockAttributes {
    showStepNumber?: boolean | undefined,
    sectionTitle?: string |undefined,
    sectionDescription?: string | undefined,
    inputLabel?: string | undefined,
}

export interface BlockEditAttributes extends BlockAttributes {}
export interface BlockViewAttributes extends Omit<BlockAttributes, 'showStepNumber'> {
    showStepNumber?: string | undefined,
    extensions: any,
    checkoutExtensionData: any,
}
