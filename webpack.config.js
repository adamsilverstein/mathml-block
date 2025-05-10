const path = require( 'path' );

const WebpackBar = require( 'webpackbar' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );

module.exports = [

	// Build the settings js..
	{
		entry: [ './src/mathml-block.js', './src/mathml-inline.js' ],
		output: {
			filename: 'mathml-block.js',
			path: path.resolve(__dirname, 'dist'),
			clean: true,
		},
		module: {
			rules: [
				{
					test: /\.js$/,

					use: [
						{
							loader: 'babel-loader',
							options: {
								presets: [ [ '@babel/env', {
									'useBuiltIns': 'entry',
									'corejs': 3,
								} ], '@babel/preset-react' ],
							}
						}
					]
				},
				{
					test: /\.css$/,
					use: [ 'style-loader', 'css-loader' ],
				},
			]
		},
		plugins: [
			new WebpackBar(
				{
					name: 'Plugin Entry Points',
					color: '#B6CD58',
				}
			),
			new ESLintPlugin({
				failOnError: true,
				extensions: ['js', 'jsx'],
			}),
		],
		performance: {
			hints: 'warning',
		},
		optimization: {
			minimize: true,
		},
		target: ['web', 'es5'],
	},

];
