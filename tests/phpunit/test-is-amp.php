<?php
/**
 * Tests for the is_amp function.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use function MathMLBlock\is_amp;

/**
 * Test case for the is_amp function.
 */
class IsAmpTest extends MathMLBlockTestCase {

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
     * Test that is_amp returns false when neither amp_is_request nor is_amp_endpoint functions exist.
     */
    public function test_is_amp_returns_false_when_no_amp_functions_exist() {
        // Ensure amp_is_request and is_amp_endpoint functions don't exist.
        $this->mock_function_exists( 'amp_is_request', false );
        $this->mock_function_exists( 'is_amp_endpoint', false );

        // Test the function.
        $this->assertFalse( is_amp() );
    }

    /**
     * Test that is_amp returns true when amp_is_request exists and returns true.
     */
    public function test_is_amp_returns_true_when_amp_is_request_returns_true() {
        // Mock amp_is_request function.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', true );

        // Test the function.
        $this->assertTrue( is_amp() );
    }

    /**
     * Test that is_amp returns false when amp_is_request exists and returns false.
     */
    public function test_is_amp_returns_false_when_amp_is_request_returns_false() {
        // Mock amp_is_request function.
        $this->mock_function_exists( 'amp_is_request', true );

        // Set the return value.
        define_test_function( 'amp_is_request', false );

        // Test the function.
        $this->assertFalse( is_amp() );
    }

    /**
     * Test that is_amp returns true when is_amp_endpoint exists and returns true.
     */
    public function test_is_amp_returns_true_when_is_amp_endpoint_returns_true() {
        // Mock is_amp_endpoint function.
        $this->mock_function_exists( 'is_amp_endpoint', true );

        // Set the return value.
        define_test_function( 'is_amp_endpoint', true );

        // Test the function.
        $this->assertTrue( is_amp() );
    }

    /**
     * Test that is_amp returns false when is_amp_endpoint exists and returns false.
     */
    public function test_is_amp_returns_false_when_is_amp_endpoint_returns_false() {
        // Mock is_amp_endpoint function.
        $this->mock_function_exists( 'is_amp_endpoint', true );

        // Set the return value.
        define_test_function( 'is_amp_endpoint', false );

        // Test the function.
        $this->assertFalse( is_amp() );
    }

    /**
     * Test that is_amp returns true when both amp functions exist and one returns true.
     */
    public function test_is_amp_returns_true_when_one_amp_function_returns_true() {
        // Mock both amp functions.
        $this->mock_function_exists( 'amp_is_request', true );
        $this->mock_function_exists( 'is_amp_endpoint', true );

        // Set the return values.
        define_test_function( 'amp_is_request', true );
        define_test_function( 'is_amp_endpoint', false );

        // Test the function.
        $this->assertTrue( is_amp() );
    }
}
