<?php

class IL_Main
{
	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		add_action( 'plugins_loaded', array('IL_Migration', 'check') );
	}

	public function admin_enqueue()
	{
		wp_enqueue_style( 'instant-locations', IL_CSS_URL . 'instant-locations.css', array(), '1.0.0' );

		wp_register_script('gmaps', 'https://maps.googleapis.com/maps/api/js?libraries=places', array(), '4.2.2', true);

        wp_register_script('instant-locations', IL_JS_URL . 'instant-locations.js', array('gmaps', 'jquery'), '1.0.0', true);

        wp_enqueue_script('instant-locations');
	}

	public function frontend_enqueue()
	{

	}

	public function i18n()
	{
		load_plugin_textdomain( 'il', false, basename( IL_DIR ) . '/lang/' );
	}
}