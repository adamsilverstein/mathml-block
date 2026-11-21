<?php
/**
 * Base test case for MathML Block plugin tests.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base test case class for all MathML Block plugin test cases.
 */
class MathMLBlockTestCase extends TestCase {

    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        parent::setUp();

        // Reset any global variables or states.
        global $wp_scripts, $wp_scripts_is, $function_exists_overrides, $is_admin_override, $test_function_values;
        $wp_scripts = null;
        $wp_scripts_is = array();
        $function_exists_overrides = array();
        $is_admin_override = null;
        $test_function_values = array();

        // Reset function_exists overrides.
        reset_function_exists_overrides();
    }

    /**
     * Tear down after each test.
     */
    protected function tearDown(): void {
        // Reset any global variables or states.
        global $wp_scripts, $wp_scripts_is, $function_exists_overrides, $is_admin_override, $test_function_values;
        $wp_scripts = null;
        $wp_scripts_is = array();
        $function_exists_overrides = array();
        $is_admin_override = null;
        $test_function_values = array();

        // Reset function_exists overrides.
        reset_function_exists_overrides();

        parent::tearDown();
    }

    /**
     * Set the return value for wp_script_is function.
     *
     * @param string $handle Script handle.
     * @param string $list   List name.
     * @param bool   $value  Return value.
     */
    protected function set_wp_script_is( $handle, $list, $value ) {
        global $wp_scripts_is;
        if ( ! isset( $wp_scripts_is ) ) {
            $wp_scripts_is = array();
        }
        if ( ! isset( $wp_scripts_is[ $handle ] ) ) {
            $wp_scripts_is[ $handle ] = array();
        }
        $wp_scripts_is[ $handle ][ $list ] = $value;
    }

    /**
     * Set the is_admin() function return value.
     *
     * @param bool $value Return value.
     */
    protected function set_is_admin( $value ) {
        global $is_admin_override;
        $is_admin_override = $value;
    }

    /**
     * Mock a function to exist or not.
     *
     * @param string $function_name Function name.
     * @param bool   $exists        Whether the function exists.
     */
    protected function mock_function_exists( $function_name, $exists = true ) {
        global $function_exists_overrides;
        if ( ! isset( $function_exists_overrides ) ) {
            $function_exists_overrides = array();
        }
        $function_exists_overrides[ $function_name ] = $exists;
    }

    /**
     * Get the WP_Scripts instance.
     *
     * @return WP_Scripts The WP_Scripts instance.
     */
    protected function get_wp_scripts() {
        return wp_scripts();
    }

    /**
     * Get the WP_Block_Type_Registry instance.
     *
     * @return WP_Block_Type_Registry The WP_Block_Type_Registry instance.
     */
    protected function get_block_type_registry() {
        return \WP_Block_Type_Registry::get_instance();
    }
}
