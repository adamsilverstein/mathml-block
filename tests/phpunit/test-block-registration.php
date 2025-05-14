<?php
/**
 * Tests for block registration function.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

use function MathMLBlock\register_block;
use const MathMLBlock\BLOCK_NAME;

/**
 * Test case for block registration function.
 */
class BlockRegistrationTest extends MathMLBlockTestCase {

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
     * Test that register_block registers the block when register_block_type function exists.
     */
    public function test_register_block_when_register_block_type_exists() {
        // Mock register_block_type function.
        $this->mock_function_exists( 'register_block_type', true );

        // Call the function.
        register_block();

        // Get the block registry.
        $registry = $this->get_block_type_registry();

        // Check that the block was registered.
        $this->assertTrue( $registry->is_registered( BLOCK_NAME ) );

        // Get the registered block.
        $block = $registry->get_registered( BLOCK_NAME );

        // Check that the block has the correct attributes.
        $this->assertObjectHasProperty( 'attributes', $block );
        $this->assertArrayHasKey( 'formula', $block->attributes );
        $this->assertEquals( 'html', $block->attributes['formula']['source'] );
        $this->assertEquals( 'div', $block->attributes['formula']['selector'] );
        $this->assertEquals( 'string', $block->attributes['formula']['type'] );

        // Check that the block has the correct render callback.
        $this->assertObjectHasProperty( 'render_callback', $block );
        $this->assertEquals( 'MathMLBlock\render_block', $block->render_callback );
    }


    /**
     * Test that register_block updates an existing block if it's already registered.
     */
    public function test_register_block_updates_existing_block() {
        // Mock register_block_type function.
        $this->mock_function_exists( 'register_block_type', true );

        // Get the block registry.
        $registry = $this->get_block_type_registry();

        // Register a block with minimal attributes.
        $registry->register( BLOCK_NAME, array(
            'attributes' => array(
                'existing_attr' => array(
                    'type' => 'string',
                ),
            ),
        ) );

        // Call the function.
        register_block();

        // Get the registered block.
        $block = $registry->get_registered( BLOCK_NAME );

        // Check that the block has both the existing and new attributes.
        $this->assertObjectHasProperty( 'attributes', $block );
        $this->assertArrayHasKey( 'existing_attr', $block->attributes );
        $this->assertArrayHasKey( 'formula', $block->attributes );

        // Check that the block has the correct render callback.
        $this->assertObjectHasProperty( 'render_callback', $block );
        $this->assertEquals( 'MathMLBlock\render_block', $block->render_callback );
    }
}
