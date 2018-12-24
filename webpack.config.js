const path = require( 'path' );

const WebpackBar = require( 'webpackbar' );

module.exports = [

	// Build the settings js..
	{
		entry:   './mathml-block.js',
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
