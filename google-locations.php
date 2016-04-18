<?php
/*
Plugin Name: Google Locations
Plugin URI: http://googlelocations.com
Description: Powerful tool to interact with Google Maps Geolocation API and save location data
Version: 1.0
Author: Tan Nguyen
Author URI: https://www.binaty.org
License: GPL2+
*/

//Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

//----------------------------------------------------------
//Define plugin URL for loading static files or doing AJAX
//------------------------------------------------------------
if ( ! defined( 'GL_URL' ) )
	define( 'GL_URL', plugin_dir_url( __FILE__ ) );

define( 'GL_JS_URL', trailingslashit( GL_URL . 'assets/js' ) );
// ------------------------------------------------------------
// Plugin paths, for including files
// ------------------------------------------------------------
if ( ! defined( 'GL_DIR' ) )
	define( 'GL_DIR', plugin_dir_path( __FILE__ ) );

define( 'GL_INC_DIR', trailingslashit( GL_DIR . 'inc' ) );

// Load the plugin's main class and assets
include GL_INC_DIR . 'helpers.php';
include GL_INC_DIR . 'class-gl-migration.php';
include GL_INC_DIR . 'class-gl-settings.php';
include GL_INC_DIR . 'class-gl-meta-box.php';
include GL_INC_DIR . 'class-gl-main.php';

new GL_Main;
register_activation_hook( __FILE__, array( 'GL_Migration', 'up' ) );
register_deactivation_hook( __FILE__, array( 'GL_Migration', 'down' ) );
add_action( 'plugins_loaded', array('GL_Migration', 'check') );