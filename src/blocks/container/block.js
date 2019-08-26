/**
 * Block: Container
 */

import './style.scss';
import './editor.scss';

import editContainer from './edit'
import saveContainer from './save'
import blockAttributes from './attributes'
import getIcon from '../../utils/get-icon'

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const el = wp.element.createElement;

/**
 * Register our Container block.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'flexblocks/container', {
	title: __( 'Container', 'flexblocks' ),
	icon: getIcon( 'container' ),
	category: 'flexblocks',
	keywords: [
		__( 'section' ),
		__( 'container' ),
		__( 'flex' ),
	],
	attributes: blockAttributes,
	supports: {
		anchor: false,
		className: false,
		customClassName: false
	},
	edit: editContainer,
	save: saveContainer,
} );