<?php
/**
 * Plugin Name:       MathML block
 * Description:       Display MathML formulas.
 * Version:           1.1.2
 * Requires at least: 5.0
 * Tested up to:      5.4
 * Requires PHP:      5.4
 * Stable tag:        trunk
 * Author:            adamsilverstein
 * Author URI:        http://tunedin.net
 * License:           GPLv2 or later
 * GitHub Plugin URI: https://github.com/adamsilverstein
 *
 * @package mathml-block
 */
namespace MathMLBlock;

 /**
  * Enqueue the admin JavaScript assets.
  */
function mathml_block_enqueue_scripts() {

	wp_enqueue_script(
		'mathml-block',
		plugin_dir_url( __FILE__ ) . 'dist/mathml-block.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-editor' ),
		'',
		true
	);

	// Filter the MathJax config string.
	$config_string = apply_filters( 'mathml_block_mathjax_config', 'TeX-MML-AM_CHTML' );

	wp_enqueue_script(
		'mathjax',
		'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=' . $config_string
	);

}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\mathml_block_enqueue_scripts' );

// Maka JavaScript translatable.
function mathml_set_up_js_translations() {
	wp_set_script_translations( 'mathml-block', 'mathml-block' );
}
add_action( 'init', __NAMESPACE__ . '\mathml_set_up_js_translations' );

/**
 * Potentially enqueue the front end mathjax script, if any mathml blocks are detected in the content.
 */
function potentially_add_front_end_mathjax_script() {
	global $post;

	// Only apply on singular pages.
	if ( ! is_singular() ) {
		return;
	}

	// Check the content for mathml blocks.
	$has_mathml_block  = strpos( $post->post_content, 'wp:mathml/mathmlblock' );
	$has_mathml_inline = strpos( $post->post_content, '<mathml>' );
	if ( false === $has_mathml_block && false === $has_mathml_inline ) {
		return;
	}

	// Filter the MathJax config string.
	$config_string = apply_filters( 'mathml_block_mathjax_config', 'TeX-MML-AM_CHTML' );

	// Enqueue the MathJax script for front end formula display.
	wp_register_script( 'mathjax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=' . $config_string );
	wp_enqueue_script( 'mathjax' );

}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\potentially_add_front_end_mathjax_script' );