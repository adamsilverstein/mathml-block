import icon from './icon';
const { createElement, Fragment } = window.wp.element;
const { registerFormatType, toggleFormat } = window.wp.richText;
const { RichTextToolbarButton, RichTextShortcut } = window.wp.blockEditor;
const { __ } = window.wp.i18n;
import './mathml-block.css';

[
	{
		name: 'mathml',
		title: __( 'MathML', 'mathml-block' ),
		character: 'm'
	},

].forEach( ( { name, title, character } ) => {
	const type = `mathml-block/${ name }`;
	registerFormatType( type, {
		title,
		tagName: name,
		className: null,
		edit( { isActive, value, onChange } ) {
			const onToggle = () => {
				onChange( toggleFormat( value, { type } ) );
				setTimeout( () => {

					// MathJax v3 API
					if ( window.MathJax && window.MathJax.typesetPromise ) {
						const elements = document.getElementsByTagName( 'mathml' );
						if ( elements && 0 < elements.length ) {
							window.MathJax.typesetPromise( Array.from( elements ) ).catch( ( err ) => {
								// eslint-disable-next-line no-console
								console.error( 'MathJax typesetting failed: ', err );
							} );
						}

					// Fallback for MathJax v2 API (for backward compatibility)
					} else if ( window.MathJax && window.MathJax.Hub ) {
						window.MathJax.Hub.Queue( [ 'Typeset', window.MathJax.Hub, document.getElementsByTagName( 'mathml' ) ] );
					}
				}, 100 );
			};

			return (
				createElement( Fragment, null,
					createElement( RichTextShortcut, {
						type: 'primary',
						character,
						onUse: onToggle
					} ),
					createElement( RichTextToolbarButton, {
						title,
						icon,
						onClick: onToggle,
						isActive,
						shortcutType: 'primary',
						shortcutCharacter: character,
						className: `toolbar-button-with-text toolbar-button__advanced-${ name }`
					} ) )
			);
		}
	} );
} );
