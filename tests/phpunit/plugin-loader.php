<?php
/**
 * Plugin loader for PHPUnit tests.
 *
 * This file includes the plugin file but skips the constant definitions
 * that would cause errors in the test environment.
 *
 * @package mathml-block
 */

namespace MathMLBlock\Tests;

// Get the plugin file content
$plugin_file = file_get_contents(dirname(dirname(__DIR__)) . '/mathml-block.php');

// Create a temporary file with our modifications
$temp_file = sys_get_temp_dir() . '/mathml-block-test.php';

// Remove the PHP opening tag
$modified_content = preg_replace('/^<\?php\s+/', '', $plugin_file);

// Remove the namespace declaration and use statements
$modified_content = preg_replace('/namespace\s+MathMLBlock;\s+use\s+WP_Block_Type_Registry;\s+use\s+WP_Scripts;\s+/s', '', $modified_content);

// Remove all constant definitions
$modified_content = preg_replace('/const\s+[A-Z_]+\s*=\s*[^;]+;\s*/s', '', $modified_content);

// Create the new PHP file content
$new_content = '<?php
namespace MathMLBlock;

use WP_Block_Type_Registry;
use WP_Scripts;

// Constants are already defined in bootstrap.php

' . $modified_content;

// Write to temp file
file_put_contents($temp_file, $new_content);

// Include the modified file
require_once $temp_file;
