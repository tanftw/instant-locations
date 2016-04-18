<?php

class GL_Main
{
	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		add_action( 'plugins_loaded', array('GL_Migration', 'check') );
	}

	public function admin_enqueue()
	{
		wp_enqueue_style( 'google-locations', GL_CSS_URL . 'google-locations.css', array(), '1.0.0' );

		wp_register_script('gmaps', 'https://maps.googleapis.com/maps/api/js?libraries=places', array(), '4.2.2', true);

        wp_register_script('google-locations', GL_JS_URL . 'google-locations.js', array('gmaps', 'jquery'), '1.0.0', true);

        wp_enqueue_script('google-locations');
	}

	public function frontend_enqueue()
	{

	}

	public function i18n()
	{
		load_plugin_textdomain( 'gl', false, basename( GL_DIR ) . '/lang/' );
	}
}