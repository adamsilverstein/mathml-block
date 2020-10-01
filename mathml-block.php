<?php
/**
 * Plugin Name:       MathML block
 * Description:       Display MathML formulas.
 * Version:           1.1.5
 * Requires at least: 5.0
 * Tested up to:      5.5
 * Requires PHP:      5.6
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

const MATHJAX_SCRIPT_HANDLE = 'mathjax';

const MATHJAX_SCRIPT_URL = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js';

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
 * Register MathJax script.
 */
function register_mathjax_script() {

	/**
	 * Filters the MathJax config string.
	 *
	 * @param string $config MathHax config.
	 */
	$config_string = apply_filters( 'mathml_block_mathjax_config', 'TeX-MML-AM_CHTML' );

	$src = add_query_arg(
		array(
			'config' => rawurlencode( $config_string )
		),
		MATHJAX_SCRIPT_URL
	);

	wp_register_script( MATHJAX_SCRIPT_HANDLE, $src, array(), null, false );

	// Maka JavaScript translatable.
	wp_set_script_translations( MATHJAX_SCRIPT_HANDLE, 'mathml-block' );
}

/**
 * Enqueue the admin JavaScript assets.
 */
function mathml_block_enqueue_scripts() {
	register_mathjax_script();
	wp_enqueue_script( MATHJAX_SCRIPT_HANDLE );

	wp_enqueue_script(
		'mathml-block',
		plugin_dir_url( __FILE__ ) . 'dist/mathml-block.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-editor' ),
		'',
		true
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\mathml_block_enqueue_scripts' );

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
 * Add async attribute to MathJax script tag.
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 *
 * @return string Script tag.
 */
function add_async_to_mathjax_script_loader_tag( $tag, $handle ) {
	if ( MATHJAX_SCRIPT_HANDLE === $handle ) {
		$tag = preg_replace( '/(?<=<script\s)/', ' async ', $tag );
	}
	return $tag;
}

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
	if ( is_admin() || ! preg_match( '#^(?P<start_div>\s*<div.*?>)(?P<formula>.+)(?P<end_div></div>\s*)$#s', $content, $matches ) ) {
		return $content;
	}

	if ( is_amp() ) {
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
	} elseif ( ! wp_script_is( MATHJAX_SCRIPT_HANDLE, 'done' ) ) {
		register_mathjax_script();
		ob_start();
		add_filter( 'script_loader_tag', __NAMESPACE__ . '\add_async_to_mathjax_script_loader_tag', 10, 2 );
		wp_scripts()->do_items( MATHJAX_SCRIPT_HANDLE );
		remove_filter( 'script_loader_tag', __NAMESPACE__ . '\add_async_to_mathjax_script_loader_tag' );
		$scripts = ob_get_clean();

		$content = $matches['start_div'] . $matches['formula'] . $scripts . $matches['end_div'];
	}
	return $content;
}
