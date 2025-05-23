<?php
/**
 * Plugin Name:       MathML block
 * Description:       Display MathML formulas.
 * Version:           2.0.0
 * Requires at least: 5.0
 * Tested up to:      6.8
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
use WP_Scripts;

const BLOCK_NAME = 'mathml/mathmlblock';

const MATHJAX_SCRIPT_HANDLE = 'mathjax';

function get_mathjax_script_url() {
	return plugin_dir_url( __FILE__ ) . 'vendor/MathJax/es5/tex-mml-chtml.js';
}

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
 *
 * @param WP_Scripts $scripts Scripts.
 */
function register_mathjax_script( WP_Scripts $scripts ) {

	/**
	 * MathJax v3 doesn't use config query parameters like v2 did.
	 */
	$scripts->add( MATHJAX_SCRIPT_HANDLE, get_mathjax_script_url(), array(), null, false );

	// Make JavaScript translatable.
	$scripts->set_translations( MATHJAX_SCRIPT_HANDLE, 'mathml-block' );
}
add_action( 'wp_default_scripts', __NAMESPACE__ . '\register_mathjax_script' );

/**
 * Add MathJax v3 configuration.
 */
function add_mathjax_config() {
	?>
	<script type="text/javascript">
	window.MathJax = {
		tex: {
			inlineMath: [['$', '$'], ['\\(', '\\)']]
		},
		options: {
			skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
			ignoreHtmlClass: 'tex2jax_ignore',
			processHtmlClass: 'tex2jax_process'
		}
	};
	</script>
	<?php
}
add_action( 'wp_head', __NAMESPACE__ . '\add_mathjax_config', 5 );

/**
 * Enqueue the admin JavaScript assets.
 */
function mathml_block_enqueue_scripts() {
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

		$style = '';
		if ( ! $printed_style ) {
			// Add same margins as .MJXc-display.
			ob_start();
			?>
			<style class="amp-mathml">
				.wp-block-mathml-mathmlblock amp-mathml { margin: 1em 0; }
			</style>
			<?php
			$style         = ob_get_clean();
			$printed_style = true;
		}

		return sprintf(
			'%s%s<amp-mathml layout="container" data-formula="%s"><span placeholder>%s</span></amp-mathml>%s',
			$matches['start_div'],
			$style,
			esc_attr( $matches['formula'] ),
			esc_html( $matches['formula'] ),
			$matches['end_div']
		);
	} elseif ( ! wp_script_is( MATHJAX_SCRIPT_HANDLE, 'done' ) ) {
		ob_start();
		add_filter( 'script_loader_tag', __NAMESPACE__ . '\add_async_to_mathjax_script_loader_tag', 10, 2 );
		wp_scripts()->do_items( MATHJAX_SCRIPT_HANDLE );
		remove_filter( 'script_loader_tag', __NAMESPACE__ . '\add_async_to_mathjax_script_loader_tag' );
		$scripts = ob_get_clean();

		$content = $matches['start_div'] . $matches['formula'] . $scripts . $matches['end_div'];
	}
	return $content;
}

/**
 * Filter content to transform inline math.
 *
 * @param string $content Content.
 * @return string Replaced content.
 */
function filter_content( $content ) {
	return preg_replace_callback(
		'#(?P<start_tag><mathml>)(?P<formula>.+)(?P<end_tag></mathml>)#s',
		static function ( $matches ) {
			if ( is_amp() ) {
				return sprintf(
					'<amp-mathml layout="container" data-formula="%s" inline><span placeholder>%s</span></amp-mathml>',
					esc_attr( $matches['formula'] ),
					esc_html( $matches['formula'] )
				);
			} else {
				wp_enqueue_script( MATHJAX_SCRIPT_HANDLE );
				return $matches['start_tag'] . $matches['formula'] . $matches['end_tag'];
			}
		},
		$content
	);
}
add_filter( 'the_content', __NAMESPACE__ . '\filter_content', 20 );

/**
 * Enable the mathml inline tag for users without the unfiltered_html capability.
 */
function enable_mathml_in_wp_kses( $allowed_tags ) {
	$allowed_tags[ 'mathml' ] = array();
	return $allowed_tags;
}
add_filter( 'wp_kses_allowed_html', __NAMESPACE__ . '\enable_mathml_in_wp_kses' );
