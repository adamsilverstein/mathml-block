
/* global MathJax */
import uuid from 'uuid/v4';

const { registerBlockType } = wp.blocks;

let loadingMathJax = false;

const renderMathML = ( id ) => {

	setTimeout( () => {
		MathJax.Hub.Queue( [ 'Typeset', MathJax.Hub, document.getElementById( id ) ] );
	}, 100 );
};

const loadAndRenderMathML = ( id ) => {
	if ( 'undefined' === typeof MathJax ) {
		if ( ! loadingMathJax ) {
			loadingMathJax = true;
			( function() {
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src  = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML';
				script.onload = renderMathML;
				document.getElementsByTagName( 'head' )[0].appendChild( script );
			}() );
		} else {
			setTimeout( () => {
				loadAndRenderMathML( id );
			}, 500 );
		}
	} else {
		renderMathML( id );
	}
};

registerBlockType( 'mathml/mathmlblock', {
	title: 'MathML',
	icon: 'list-view',
	category: 'common',
	attributes: {
		formula: {
			source: 'html',
			selector: 'div',
			type: 'string',
		},
	},

	edit: ( props ) => {

		const { isSelected, attributes, setAttributes, className } = props;
		const { formula } = attributes;
		const id = uuid();

		loadAndRenderMathML( id );

		if ( isSelected ) {
			return (
				<div className={ className }>
					<textarea
						className="mathml-formula"
						tagname="div"
						onChange={ ( event ) => {
							setAttributes( { formula: event.target.value } );
						} }
						value={ formula }
						style={ { width: '100%' } }
					/>
				</div>
			);
		} else {
			return (
				<div
					id={ id }
					className="mathml-block"
				>
					{ formula }
				</div>
			);
		}
	},

	save: function save( { attributes, className } ) {
		const { formula } = attributes;

		return (
			<div className={ className }>
				{ formula }
			</div>
		);
	},
} );


