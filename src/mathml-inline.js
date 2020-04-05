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
					MathJax.Hub.Queue( [ 'Typeset', MathJax.Hub, document.getElementsByTagName( 'mathml' ) ] );
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
