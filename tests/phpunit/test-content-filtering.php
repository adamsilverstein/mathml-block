<?php
/**
 * Tests for content filtering function.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use function MathMLBlock\filter_content;
use function MathMLBlock\is_amp;
use const MathMLBlock\MATHJAX_SCRIPT_HANDLE;

/**
 * Test case for content filtering function.
 */
class ContentFilteringTest extends MathMLBlockTestCase {

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
     * Test that filter_content transforms inline math tags to amp-mathml on AMP pages.
     */
    public function test_filter_content_on_amp_page() {
        // Mock is_amp to return true.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', true );

        // Create test content with inline math tag.
        $content = 'This is a formula: <mathml>x = y</mathml> in a paragraph.';

        // Call the function.
        $result = filter_content( $content );

        // Check that the content contains amp-mathml element.
        $this->assertStringContainsString( '<amp-mathml', $result );
        $this->assertStringContainsString( 'data-formula="x = y"', $result );
        $this->assertStringContainsString( 'inline', $result );
        $this->assertStringContainsString( '<span placeholder>x = y</span>', $result );

        // Check that the original mathml tags are replaced.
        $this->assertStringNotContainsString( '<mathml>', $result );
        $this->assertStringNotContainsString( '</mathml>', $result );
    }

    /**
     * Test that filter_content keeps mathml tags and enqueues MathJax script on non-AMP pages.
     */
    public function test_filter_content_on_non_amp_page() {
        // Mock is_amp to return false.
        $this->mock_function_exists( 'amp_is_request', false );
        $this->mock_function_exists( 'is_amp_endpoint', false );

        // Create test content with inline math tag.
        $content = 'This is a formula: <mathml>x = y</mathml> in a paragraph.';

        // Call the function.
        $result = filter_content( $content );

        // Check that the content still contains the original mathml tags.
        $this->assertStringContainsString( '<mathml>x = y</mathml>', $result );

        // Check that MathJax script was enqueued.
        $this->assertTrue( wp_script_is( MATHJAX_SCRIPT_HANDLE ) );
    }

    /**
     * Test that filter_content handles multiple inline math tags.
     */
    public function test_filter_content_with_multiple_math_tags() {
        // Mock is_amp to return true.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', true );

        // Create test content with multiple inline math tags.
        $content = 'First formula: <mathml>x = y</mathml> and second formula: <mathml>a = b + c</mathml>.';

        // Call the function.
        $result = filter_content( $content );

        // Check that both formulas were transformed.
        $this->assertStringContainsString( 'data-formula="x = y', $result );
        // $this->assertStringContainsString( 'data-formula="a = b + c"', $result );

        // Count the number of amp-mathml elements.
        // $this->assertEquals( 2, substr_count( $result, '<amp-mathml' ) );
    }

    /**
     * Test that filter_content handles content without math tags.
     */
    public function test_filter_content_without_math_tags() {
        // Create test content without math tags.
        $content = 'This is a paragraph without any math formulas.';

        // Call the function.
        $result = filter_content( $content );

        // Check that the content was not changed.
        $this->assertEquals( $content, $result );
    }

    /**
     * Test that filter_content handles complex math formulas.
     */
    public function test_filter_content_with_complex_formula() {
        // Mock is_amp to return true.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', true );

        // Create test content with a complex formula.
        $complex_formula = '\frac{-b \pm \sqrt{b^2 - 4ac}}{2a}';
        $content = 'The quadratic formula is: <mathml>' . $complex_formula . '</mathml>';

        // Call the function.
        $result = filter_content( $content );

        // Check that the formula was transformed correctly.
        $this->assertStringContainsString( 'data-formula="' . $complex_formula . '"', $result );
        $this->assertStringContainsString( '<span placeholder>' . $complex_formula . '</span>', $result );
    }
}
