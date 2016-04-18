<?php

class GL_Main
{
	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		add_action( 'plugins_loaded', array( $this, 'i18n' ) );
	}

	public function admin_enqueue()
	{

	}

	public function frontend_enqueue()
	{

	}

	public function i18n()
	{
		load_plugin_textdomain( 'gl', false, basename( GL_DIR ) . '/lang/' );
	}
}