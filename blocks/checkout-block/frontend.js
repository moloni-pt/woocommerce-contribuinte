import { registerCheckoutBlock } from '@woocommerce/blocks-checkout';

import metadata from './block.json';
import { lazy } from '@wordpress/element';

registerCheckoutBlock({
    metadata,
    component: lazy(() => import('./block')),
});
