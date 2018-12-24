
/* global MathJax */
import uuid from 'uuid/v4';

const { registerBlockType } = wp.blocks;

console.log( 'registerBlockType' );

const id = uuid();

let loadingMathJax = false;

const renderMathML = () => {
	MathJax.Hub.Queue( [ 'Typeset', MathJax.Hub, id ] );
};

const loadAndRenderMathML = () => {
	if ( ! MathJax &&  ! loadingMathJax ) {
		loadingMathJax = true;
		(function () {
			var script = document.createElement( 'script' );
			script.type = 'text/javascript';
			script.src  = 'https://example.com/MathJax.js?config=TeX-AMS-MML_CHTML';
			script.onload = renderMathML;
			document.getElementsByTagName( 'head' )[0].appendChild( script );
		})();
	} else {
		renderMathML();
	}
};

registerBlockType( 'mathml', {
	title: 'MathML',
	icon: 'list-view',
	category: 'inline',
	attributes: {
		formula: {
			source: 'children',
			selector: 'p',
			type: 'string',
		},
	}

	edit = ( { isSelected, attributes, setAttributes, className } ) => {
		const { formula } = attributes;

		loadAndRenderMathML();

		if ( isSelected ) {
			return (
				<div className={ className }>
					<RichText
					className="mathml-formula"
					value={ formula }
					onChange={ ( content ) => setAttributes( { content } ) } />
				</div>
			);
			} else {
				return <div id={ id } className="mathml-block">{ formula }</div>
			}
	},

	save( { attributes } ) {
		const { formula } = attributes;
		return (
			<p id={ id } className="mathml-block">{ formula }</p>
		);	},
} );

