<?php
/**
 * Main class for Instant Locations plugin
 *
 * @author Tan Nguyen <tan@binaty.org>
 */
class IL_Main
{
	/**
	 * Constructor method. Only to define hooks
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Load admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Supports I18N
		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		// When plugin is loaded. Check if database has changed. If so, run migration.
		add_action( 'plugins_loaded', array('IL_Migration', 'check') );
	}

	/**
	 * Enqueue styles and script in admin.
	 * 
	 * @return void
	 */
	public function admin_enqueue()
	{
		// Auto complete configs. Use it to pass to `window.geo_config` variable 
        $geo_config = il_setting( 'geo_config' );

        if (empty($geo_config['componentRestrictions']['country']))
        	unset($geo_config['componentRestrictions']);

        if (empty($geo_config['types']))
        	unset($geo_config['types']);

        $api_key = il_setting( 'api_key' );

        $query_string = '';
        if ( ! empty( $api_key ) && $api_key != 'YOUR API KEY HERE')
        	$query_string = '&key=' . $api_key;

		wp_enqueue_style( 'instant-locations', IL_CSS_URL . 'instant-locations.css', array(), '1.0.0' );

		wp_register_script('gmaps', 'https://maps.googleapis.com/maps/api/js?libraries=places' . $query_string, array(), '4.2.2', true);

        wp_register_script('instant-locations', IL_JS_URL . 'instant-locations.js', array('gmaps', 'jquery'), '1.0.0', true);

        wp_localize_script( 'instant-locations', 'geo_config', $geo_config );

        wp_enqueue_script('instant-locations');
	}

	public function i18n()
	{
		load_plugin_textdomain( 'instant-locations', false, basename( IL_DIR ) . '/lang/' );
	}
}