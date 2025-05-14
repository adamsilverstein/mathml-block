<?php
/**
 * PHPUnit bootstrap file
 *
 * @package mathml-block
 */

// Require composer autoloader.
require_once dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php';

// Define constants for testing.
define( 'MATHML_BLOCK_PHPUNIT', true );
define( 'WP_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) );

// Define constants from the plugin that use functions.
define( 'MathMLBlock\\BLOCK_NAME', 'mathml/mathmlblock' );
define( 'MathMLBlock\\MATHJAX_SCRIPT_HANDLE', 'mathjax' );
define( 'MathMLBlock\\MATHJAX_SCRIPT_URL', 'https://example.com/wp-content/plugins/mathml-block/vendor/MathJax/es5/tex-mml-chtml.js' );

/**
 * Mock WordPress functions and classes that are used in the plugin.
 */

// Mock plugin_dir_url function.
if ( ! function_exists( 'plugin_dir_url' ) ) {
    function plugin_dir_url( $file ) {
        return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
    }
}

// Mock add_action function.
if ( ! function_exists( 'add_action' ) ) {
    function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        return true;
    }
}

// Mock add_filter function.
if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        return true;
    }
}

// Mock remove_filter function.
if ( ! function_exists( 'remove_filter' ) ) {
    function remove_filter( $hook, $callback, $priority = 10 ) {
        return true;
    }
}

// Mock function_exists function to control behavior in tests.
if ( ! function_exists( 'override_function_exists' ) ) {
    function override_function_exists( $function_name ) {
        global $function_exists_overrides;
        if ( ! isset( $function_exists_overrides ) ) {
            $function_exists_overrides = array();
        }
        $function_exists_overrides[ $function_name ] = true;
    }
}

if ( ! function_exists( 'reset_function_exists_overrides' ) ) {
    function reset_function_exists_overrides() {
        global $function_exists_overrides;
        $function_exists_overrides = array();
    }
}

// Override the original function_exists.
if ( ! function_exists( 'function_exists' ) ) {
    function function_exists( $function_name ) {
        global $function_exists_overrides;
        if ( isset( $function_exists_overrides ) && isset( $function_exists_overrides[ $function_name ] ) ) {
            return $function_exists_overrides[ $function_name ];
        }
        return \function_exists( $function_name );
    }
}

// Mock wp_enqueue_script function.
if ( ! function_exists( 'wp_enqueue_script' ) ) {
    function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
        global $wp_scripts_is;
        if ( ! isset( $wp_scripts_is ) ) {
            $wp_scripts_is = array();
        }
        if ( ! isset( $wp_scripts_is[ $handle ] ) ) {
            $wp_scripts_is[ $handle ] = array();
        }
        $wp_scripts_is[ $handle ]['enqueued'] = true;
        return true;
    }
}

// Mock wp_script_is function.
if ( ! function_exists( 'wp_script_is' ) ) {
    function wp_script_is( $handle, $list = 'enqueued' ) {
        global $wp_scripts_is;
        if ( isset( $wp_scripts_is[ $handle ][ $list ] ) ) {
            return $wp_scripts_is[ $handle ][ $list ];
        }
        return false;
    }
}

// Mock WP_Scripts class.
if ( ! class_exists( 'WP_Scripts' ) ) {
    class WP_Scripts {
        public $registered = array();
        public $queue = array();
        public $translations = array();

        public function add( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
            $this->registered[ $handle ] = array(
                'src' => $src,
                'deps' => $deps,
                'ver' => $ver,
                'in_footer' => $in_footer,
            );
            return true;
        }

        public function set_translations( $handle, $domain ) {
            $this->translations[ $handle ] = $domain;
            return true;
        }

        public function do_items( $handle ) {
            global $wp_scripts_is;
            if ( ! isset( $wp_scripts_is ) ) {
                $wp_scripts_is = array();
            }
            if ( ! isset( $wp_scripts_is[ $handle ] ) ) {
                $wp_scripts_is[ $handle ] = array();
            }
            $wp_scripts_is[ $handle ]['done'] = true;
            $this->queue[] = $handle;
            return true;
        }
    }
}

// Mock WP_Block_Type_Registry class.
if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
    class WP_Block_Type_Registry {
        private static $instance = null;
        private $registered_block_types = array();

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function register( $name, $args ) {
            $block_type = new \stdClass();
            $block_type->name = $name;
            foreach ( $args as $key => $value ) {
                $block_type->$key = $value;
            }
            $this->registered_block_types[ $name ] = $block_type;
            return $block_type;
        }

        public function is_registered( $name ) {
            return isset( $this->registered_block_types[ $name ] );
        }

        public function get_registered( $name ) {
            if ( $this->is_registered( $name ) ) {
                return $this->registered_block_types[ $name ];
            }
            return null;
        }

        public function unregister( $name ) {
            if ( $this->is_registered( $name ) ) {
                unset( $this->registered_block_types[ $name ] );
                return true;
            }
            return false;
        }
    }
}

// Mock register_block_type function.
if ( ! function_exists( 'register_block_type' ) ) {
    function register_block_type( $name, $args = array() ) {
        $registry = WP_Block_Type_Registry::get_instance();
        return $registry->register( $name, $args );
    }
}

// Mock is_admin function.
if ( ! function_exists( 'is_admin' ) ) {
    function is_admin() {
        global $is_admin_override;
        if ( isset( $is_admin_override ) ) {
            return $is_admin_override;
        }
        return false;
    }
}

// Mock esc_attr function.
if ( ! function_exists( 'esc_attr' ) ) {
    function esc_attr( $text ) {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}

// Mock esc_html function.
if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( $text ) {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}

// Mock wp_scripts function.
if ( ! function_exists( 'wp_scripts' ) ) {
    function wp_scripts() {
        global $wp_scripts;
        if ( ! isset( $wp_scripts ) ) {
            $wp_scripts = new WP_Scripts();
        }
        return $wp_scripts;
    }
}

/**
 * Helper function to define test functions in the global namespace.
 *
 * @param string $function_name The function name.
 * @param mixed  $return_value  The return value.
 */
function define_test_function( $function_name, $return_value ) {
    global $test_function_values;
    if ( ! isset( $test_function_values ) ) {
        $test_function_values = array();
    }
    $test_function_values[ $function_name ] = $return_value;
}

/**
 * Get the return value for a test function.
 *
 * @param string $function_name The function name.
 * @return mixed The return value.
 */
function get_test_function_value( $function_name ) {
    global $test_function_values;
    if ( isset( $test_function_values ) && isset( $test_function_values[ $function_name ] ) ) {
        return $test_function_values[ $function_name ];
    }
    return null;
}

// Define global test functions.
if ( ! function_exists( 'amp_is_request' ) ) {
    function amp_is_request() {
        return get_test_function_value( 'amp_is_request' );
    }
}

if ( ! function_exists( 'is_amp_endpoint' ) ) {
    function is_amp_endpoint() {
        return get_test_function_value( 'is_amp_endpoint' );
    }
}

// Include the base test case class.
require_once __DIR__ . '/class-test-case.php';

// Include the plugin file, but skip the constant definitions.
require_once __DIR__ . '/plugin-loader.php';
