import { v4 as uuid } from 'uuid';

const { __ }                = wp.i18n;
const { registerBlockType } = wp.blocks;

const renderMathML = ( id ) => {
	setTimeout( () => {
		MathJax.Hub.Queue( [ 'Typeset', MathJax.Hub, document.getElementById( id ) ] );
	}, 100 );
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

		renderMathML( id );

		if ( isSelected ) {
			return (
				<div className={ className }>
					<label htmlFor={ id }>{ __( 'MathML formula:', 'mathml-block' ) }</label>
					<textarea
						id={ id }
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


