/**
 * External dependencies
 */
import { registerPlugin } from '@wordpress/plugins';

const render = () => {};

registerPlugin('woocommerce-contribuinte', {
	render,
	scope: 'woocommerce-checkout',
});
