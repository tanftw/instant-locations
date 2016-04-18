<?php

/**
 * Default Plugin Settings
 * 
 * @return array
 */
function gl_default_settings()
{
	$defaults = array(
		// Allows user config geo auto complete
		'geo_config' => array(),

		'api_key' => 'YOUR_API_KEY_HERE',

		'fields' => array(
			'administrative_area_level_1' => array(
				'show' 		=> 1,
				'title' 	=> 'State',
				'long_name' => true
			)
		)
	);

	return apply_filters( 'google_locations', $defaults );
}

/**
 * Get plugin setting
 * 
 * @param  Mixed $field Field name, if empty, return whole settings array
 * 
 * @return Mixed
 */
function gl_setting( $field = null )
{
 	$settings = get_option( 'google_locations' );

 	$defaults = gl_default_settings();

 	if ( empty( $settings ) || ! is_array( $settings ) )
 		$settings = $defaults;

	if ( is_null( $field ) )
		return $settings;

	if ( isset( $settings[$field] ) )
		return $settings[$field];

	if ( isset( $defaults[$field] ) )
		return $defaults[$field];

	return null;
}

if ( ! function_exists('gl_set_data') )
{
	function gl_set_data( $object_id, $address_component, $value, $object_type = 'post' )
	{
		global $wpdb;

		return $wpdb->update( $wpdb->prefix . 'locations', [$address_component => $value], compact( 'object_id', 'object_name' ) );
	}
}

if ( ! function_exists( 'gl_post_set_data' ) ) 
{
	function gl_post_set_data( $post_id, $address_component, $value )
	{
		return gl_set_data( $post_id, $address_component, $value );
	}
}

if ( ! function_exists( 'gl_get' ) )
{
	function gl_get_data( $object_id, $address_component = null, $default = null)
	{
		global $wpdb;

		if ( null === $address_component )
			return $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'locations WHERE id = ' . $object_id);

		// Todo: Fix SQL Injection
		if ( is_array($address_component) )
		{
			$address_component = implode(',', $address_component);

			return $wpdb->get_row("SELECT $address_component FROM {$wpdb->prefix}locations WHERE id = {$object_id}");
		}

		return $wpdb->get_var("SELECT $address_component FROM {$wpdb->prefix}locations WHERE id = {$object_id}");
	}
}