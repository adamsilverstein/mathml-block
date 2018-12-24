<?php
/**
 * Plugin Name:       MathML Block for Gutenberg
 * Description:       Display MathML formulas.
 * Version:           1.0.0
 * Requires at least: 5.0.0
 * Tested up to:      5.0.0
 * Requires PHP:      5.4
 * Stable tag:        5.0.0
 * Author:            adamsilverstein
 * Author URI:        http://tunedin.net
 * License:           GPLv2 or later
 * GitHub Plugin URI: https://github.com/adamsilverstein
 *
 * @package mathml-block
 */

function mathml_block_enqueue_scripts() {
	wp_enqueue_script(
		'mathml-block',
		plugin_dir_url( __FILE__ ) . 'dist/mathml-block.js',
		array( 'wp-blocks' ),
		'',
		true
	);
}
add_action( 'admin_enqueue_scripts', 'mathml_block_enqueue_scripts' );
