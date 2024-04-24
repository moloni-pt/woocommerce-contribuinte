/**
 * Links of interest
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import Save from './save';
import metadata from './block.json';
import BlockIcon from "../common/icons/BlockIcon";
import setCategoryIcon from "../common/helpers/setCategoryIcon"

setCategoryIcon();

registerBlockType(metadata.name, {
    icon: {
        src: BlockIcon
    },
    edit: Edit,
    save: Save,
});
