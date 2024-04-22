import BlockIcon from "../icons/BlockIcon";

const setCategoryIcon = () => {
    wp.blocks.updateCategory( 'contribuinte-checkout-category', { icon: BlockIcon } );
};

export default setCategoryIcon;
