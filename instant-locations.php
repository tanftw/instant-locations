<?php
/*
Plugin Name: Instant Locations
Plugin URI: http://instantlocations.com
Description: Instant & Auto populate location data with the power of Google Maps API.
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
if ( ! defined( 'IL_URL' ) )
	define( 'IL_URL', plugin_dir_url( __FILE__ ) );

define( 'IL_JS_URL', trailingslashit( IL_URL . 'assets/js' ) );
define( 'IL_CSS_URL', trailingslashit( IL_URL . 'assets/css' ) );
// ------------------------------------------------------------
// Plugin paths, for including files
// ------------------------------------------------------------
if ( ! defined( 'IL_DIR' ) )
	define( 'IL_DIR', plugin_dir_path( __FILE__ ) );

define( 'IL_INC_DIR', trailingslashit( IL_DIR . 'inc' ) );

// Load the plugin's main class and assets
include IL_INC_DIR . 'helpers.php';
include IL_INC_DIR . 'class-il-migration.php';
include IL_INC_DIR . 'class-il-settings.php';
include IL_INC_DIR . 'class-il-meta-box.php';
include IL_INC_DIR . 'class-il-main.php';

new IL_Main;

register_activation_hook( __FILE__, array( 'IL_Migration', 'up' ) );
register_deactivation_hook( __FILE__, array( 'IL_Migration', 'down' ) );