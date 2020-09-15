<?php
/**
 * Plugin Name:       MathML block
 * Description:       Display MathML formulas.
 * Version:           1.1.2
 * Requires at least: 5.0
 * Tested up to:      5.5
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

use WP_Block_Type_Registry;

const BLOCK_NAME = 'mathml/mathmlblock';

/**
 * Determine whether the response will be an AMP page.
 *
 * @return bool
 */
function is_amp() {
	return (
		( function_exists( 'amp_is_request' ) && \amp_is_request() )
		||
		( function_exists( 'is_amp_endpoint' ) && \is_amp_endpoint() )
	);
}

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

	// Only apply on singular pages which are not served as AMP.
	if ( ! is_singular() || is_amp() ) {
		return;
	}

	// Check the content for mathml blocks.
	$has_mathml_block  = strpos( $post->post_content, 'wp:' . BLOCK_NAME );
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

/**
 * Register block.
 */
function register_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$registry = WP_Block_Type_Registry::get_instance();

	// @todo This can probably be de-duplicated in the JS code with registerBlockType.
	$attributes = array(
		'formula' => array(
			'source'   => 'html',
			'selector' => 'div',
			'type'     => 'string',
		),
	);

	if ( $registry->is_registered( BLOCK_NAME ) ) {
		$block                  = $registry->get_registered( BLOCK_NAME );
		$block->render_callback = __NAMESPACE__ . '\render_block';
		$block->attributes      = array_merge( $block->attributes, $attributes );
	} else {
		register_block_type(
			BLOCK_NAME,
			[
				'render_callback' => __NAMESPACE__ . '\render_block',
				'attributes'      => $attributes,
			]
		);
	}
}
add_action( 'init', __NAMESPACE__ . '\register_block' );

/**
 * Render block.
 *
 * Creates an <amp-mathml> element on AMP responses.
 *
 * @param array  $attributes Attributes.
 * @param string $content    Content.
 *
 * @return string Rendered block.
 */
function render_block( $attributes, $content = '' ) {
	if ( is_amp() && preg_match( '#^(?P<start_div>\s*<div.*?>)(?P<formula>.+)(?P<end_div></div>\s*)$#s', $content, $matches ) ) {
		static $printed_style = false;
		if ( ! $printed_style ) {
			// Add same margins as .MJXc-display.
			?>
			<style class="amp-mathml">
				.wp-block-mathml-mathmlblock amp-mathml { margin: 1em 0; }
			</style>
			<?php
			$printed_style = true;
		}

		return sprintf(
			'%s<amp-mathml layout="container" data-formula="%s"><span placeholder>%s</span></amp-mathml>%s',
			$matches['start_div'],
			esc_attr( $matches['formula'] ),
			esc_html( $matches['formula'] ),
			$matches['end_div']
		);
	}
	return $content;
}
