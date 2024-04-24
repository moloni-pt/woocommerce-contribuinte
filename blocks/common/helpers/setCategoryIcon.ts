import BlockIcon from "../icons/BlockIcon";

/**
 * Set our category icon
 *
 * @see https://mediaron.com/adding-icons-for-your-gutenberg-categories/
 */
export default function setCategoryIcon(): void {
    // @ts-ignore
    wp.blocks.updateCategory('contribuinte-checkout-category', { icon: BlockIcon });
};
