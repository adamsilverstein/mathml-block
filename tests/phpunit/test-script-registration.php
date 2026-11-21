<?php
/**
 * Tests for script registration functions.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use function MathMLBlock\register_mathjax_script;
use function MathMLBlock\add_mathjax_config;
use function MathMLBlock\mathml_block_enqueue_scripts;
use function MathMLBlock\add_async_to_mathjax_script_loader_tag;
use const MathMLBlock\MATHJAX_SCRIPT_HANDLE;

/**
 * Test case for script registration functions.
 */
class ScriptRegistrationTest extends MathMLBlockTestCase {

    /**
     * Test that register_mathjax_script registers the MathJax script.
     */
    public function test_register_mathjax_script() {
        // Get WP_Scripts instance.
        $scripts = $this->get_wp_scripts();

        // Call the function.
        register_mathjax_script( $scripts );

        // Check that the script was registered.
        $this->assertArrayHasKey( MATHJAX_SCRIPT_HANDLE, $scripts->registered );

        // Check that translations were set.
        $this->assertArrayHasKey( MATHJAX_SCRIPT_HANDLE, $scripts->translations );
        $this->assertEquals( 'mathml-block', $scripts->translations[MATHJAX_SCRIPT_HANDLE] );
    }

    /**
     * Test that add_mathjax_config outputs the correct script tag.
     */
    public function test_add_mathjax_config() {
        // Start output buffering.
        ob_start();

        // Call the function.
        add_mathjax_config();

        // Get the output.
        $output = ob_get_clean();

        // Check that the output contains the expected script tag.
        $this->assertStringContainsString( '<script type="text/javascript">', $output );
        $this->assertStringContainsString( 'window.MathJax = {', $output );
        $this->assertStringContainsString( 'tex: {', $output );
        $this->assertStringContainsString( 'inlineMath: [[\'$\', \'$\'], [\'\\\\(\', \'\\\\)\']]', $output );
        $this->assertStringContainsString( 'options: {', $output );
        $this->assertStringContainsString( 'skipHtmlTags: [\'script\', \'noscript\', \'style\', \'textarea\', \'pre\']', $output );
        $this->assertStringContainsString( 'ignoreHtmlClass: \'tex2jax_ignore\'', $output );
        $this->assertStringContainsString( 'processHtmlClass: \'tex2jax_process\'', $output );
        $this->assertStringContainsString( '</script>', $output );
    }

    /**
     * Test that mathml_block_enqueue_scripts enqueues the scripts.
     */
    public function test_mathml_block_enqueue_scripts() {
        // Call the function.
        mathml_block_enqueue_scripts();

        // Check that wp_enqueue_script was called for MathJax.
        $this->assertTrue( wp_script_is( MATHJAX_SCRIPT_HANDLE ) );
    }

    /**
     * Test that add_async_to_mathjax_script_loader_tag adds the async attribute.
     */
    public function test_add_async_to_mathjax_script_loader_tag() {
        // Test with MathJax script.
        $tag = '<script src="' . plugin_dir_url( __FILE__ ) . 'vendor/MathJax/es5/tex-mml-chtml.js' . '"></script>';
        $result = add_async_to_mathjax_script_loader_tag( $tag, MATHJAX_SCRIPT_HANDLE );

        // Check that the async attribute was added.
        $this->assertStringContainsString( ' async ', $result );
        $this->assertEquals( '<script async src="' . plugin_dir_url( __FILE__ ) . 'vendor/MathJax/es5/tex-mml-chtml.js' . '"></script>', $result );

        // Test with a different script.
        $other_tag = '<script src="other-script.js"></script>';
        $other_result = add_async_to_mathjax_script_loader_tag( $other_tag, 'other-script' );

        // Check that the async attribute was not added.
        $this->assertEquals( $other_tag, $other_result );
    }
}
