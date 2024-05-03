// @ts-ignore
import { useBlockProps } from '@wordpress/block-editor';
import React from 'react';

export default function Save() {
	return <div { ...useBlockProps.save() } />;
}
