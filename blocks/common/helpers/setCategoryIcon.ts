import BlockIcon from "../icons/BlockIcon";
import { updateCategory } from '@wordpress/blocks';

/**
 * Set our category icon
 *
 * @see https://mediaron.com/adding-icons-for-your-gutenberg-categories/
 */
export default function setCategoryIcon(): void {
    updateCategory('contribuinte-checkout-category', { icon: BlockIcon });
};
