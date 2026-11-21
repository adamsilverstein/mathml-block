<?php
/**
 * Tests for the render_block function.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use function MathMLBlock\render_block;
use function MathMLBlock\is_amp;
use const MathMLBlock\MATHJAX_SCRIPT_HANDLE;

/**
 * Test case for the render_block function.
 */
class RenderBlockTest extends MathMLBlockTestCase {

    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        parent::setUp();

        // Remove any previously defined functions.
        global $function_exists_overrides, $test_function_values;
        $function_exists_overrides = array();
        $test_function_values = array();
    }

    /**
     * Test that render_block returns the content unchanged when in admin.
     */
    public function test_render_block_in_admin() {
        // Set is_admin to true.
        $this->set_is_admin( true );

        // Create test content.
        $content = '<div class="wp-block-mathml-mathmlblock">x = y</div>';

        // Call the function.
        $result = render_block( array(), $content );

        // Check that the content was not changed.
        $this->assertEquals( $content, $result );
    }

    /**
     * Test that render_block returns the content unchanged when content doesn't match the expected pattern.
     */
    public function test_render_block_with_invalid_content() {
        // Set is_admin to false.
        $this->set_is_admin( false );

        // Create test content that doesn't match the expected pattern.
        $content = '<p>This is not a MathML block.</p>';

        // Call the function.
        $result = render_block( array(), $content );

        // Check that the content was not changed.
        $this->assertEquals( $content, $result );
    }

    /**
     * Test that render_block adds amp-mathml element when on AMP page.
     */
    public function test_render_block_on_amp_page() {
        // Set is_admin to false.
        $this->set_is_admin( false );

        // Mock is_amp to return true.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', true );

        // Create test content.
        $content = '<div class="wp-block-mathml-mathmlblock">x = y</div>';

        // Call the function.
        $result = render_block( array(), $content );

        // Check that the content contains amp-mathml element.
        $this->assertStringContainsString( '<amp-mathml', $result );
        $this->assertStringContainsString( 'data-formula="x = y"', $result );
        $this->assertStringContainsString( '<span placeholder>x = y</span>', $result );

        // Check that the style tag is included (first time only).
        $this->assertStringContainsString( '<style class="amp-mathml">', $result );

        // Call the function again to check that the style tag is not included again.
        $result2 = render_block( array(), $content );

        // Check that the style tag is not included again.
        $this->assertStringNotContainsString( '<style class="amp-mathml">', $result2 );
    }

    /**
     * Test that render_block adds MathJax script when not on AMP page and script is not already done.
     */
    public function test_render_block_adds_mathjax_script() {
        // Set is_admin to false.
        $this->set_is_admin( false );

        // Mock is_amp to return false.
        $this->mock_function_exists( 'amp_is_request', false );
        $this->mock_function_exists( 'is_amp_endpoint', false );

        // Set wp_script_is to return false for MathJax script.
        $this->set_wp_script_is( MATHJAX_SCRIPT_HANDLE, 'done', false );

        // Create test content.
        $content = '<div class="wp-block-mathml-mathmlblock">x = y</div>';

        // Start output buffering to capture the script output.
        ob_start();

        // Call the function.
        $result = render_block( array(), $content );

        // Get the output buffer.
        $output = ob_get_clean();

        // Check that the content contains the original formula.
        $this->assertStringContainsString( 'x = y', $result );
    }

    /**
     * Test that render_block does not add MathJax script when not on AMP page but script is already done.
     */
    public function test_render_block_does_not_add_mathjax_script_when_already_done() {
        // Set is_admin to false.
        $this->set_is_admin( false );

        // Mock is_amp to return false.
        $this->mock_function_exists( 'amp_is_request', false );
        $this->mock_function_exists( 'is_amp_endpoint', false );

        // Set wp_script_is to return true for MathJax script.
        $this->set_wp_script_is( MATHJAX_SCRIPT_HANDLE, 'done', true );

        // Create test content.
        $content = '<div class="wp-block-mathml-mathmlblock">x = y</div>';

        // Call the function.
        $result = render_block( array(), $content );

        // Check that the content contains the original formula.
        $this->assertStringContainsString( 'x = y', $result );

        // Check that the script was not added.
        $this->assertStringNotContainsString( '<script', $result );
    }
}
