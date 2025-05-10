const path = require( 'path' );

const WebpackBar = require( 'webpackbar' );

module.exports = [

	// Build the settings js..
	{
		// Set Node.js crypto configuration for newer Node.js versions
		node: {
			crypto: true,
		},
		entry: [ './src/mathml-block.js', './src/mathml-inline.js' ],
		output: {
			filename: 'mathml-block.js',
			path: __dirname + '/dist/',
		},
		module: {
			rules: [
				{
					test: /\.js$/,

					use: [
						{
							loader: 'babel-loader',
							query: {
								presets: [ [ '@babel/env', {
									'useBuiltIns': 'entry',
									'corejs': 3,
								} ], '@babel/preset-react' ],
							}
						},
						{
							loader: 'eslint-loader',
							options: {
								failOnError: true,
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
		plugins: [ new WebpackBar(
			{
				name: 'Plugin Entry Points',
				color: '#B6CD58',
			}
		) ],
	},

];
