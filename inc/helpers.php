<?php

/**
 * Default Plugin Settings
 * 
 * @return array
 */
function il_default_settings()
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

	return apply_filters( 'il_default_settings', $defaults );
}

/**
 * Get plugin setting
 * 
 * @param  Mixed $field Field name, if empty, return whole settings array
 * 
 * @return Mixed
 */
function il_setting( $field = null )
{
 	$settings = get_option( 'instant_locations' );

 	$defaults = il_default_settings();

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

if ( ! function_exists('il_set_data') )
{
	function il_set_data( $object_id, $address_component, $value = '', $object_type = 'post' )
	{
		global $wpdb;

		$object_id = intval( $object_id );

		if ( ! is_array( $address_component ) && ! empty( $value ) )
			$address_component = array($address_component => $value);

		if ( isset( $address_component['lat'] ) && isset( $address_component['lng'] ) )
			$address_component['geometry'] = $address_component['lat'] . ',' . $address_component['lng'];



		$has_data = $wpdb->get_var( $wpdb->prepare( "
			SELECT 1 
			FROM {$wpdb->prefix}locations 
			WHERE object_id = %d
			AND object_type = %s",
			$object_id,
			$object_type
		) );

		if ($has_data)
			return $wpdb->update( $wpdb->prefix . 'locations', $address_component, compact( 'object_id', 'object_type' ) );
		
		$address_component['object_id'] 	= $object_id;
		$address_component['object_type'] 	= $object_type;

		return $wpdb->insert( $wpdb->prefix . 'locations', $address_component);
	}
}

if ( ! function_exists( 'il_post_set_data' ) ) 
{
	function il_post_set_data( $post_id, $address_component, $value = '' )
	{
		return il_set_data( $post_id, $address_component, $value );
	}
}

if ( ! function_exists( 'il_get_data' ) )
{
	function il_get_data( $object_id, $address_component = null, $object_type = 'post')
	{
		global $wpdb;

		$object_type = $object_type === null || empty( $object_type ) ? 'post' : $object_type;

		$location = $wpdb->get_row( $wpdb->prepare( "
			SELECT * 
			FROM {$wpdb->prefix}locations 
			WHERE object_id = %d
			AND object_type = %s
			LIMIT 1
			", $object_id, $object_type 
		), ARRAY_A );

		if ( is_string( $address_component ) )
			return $location[$address_component];

		return $location;
	}
}

if ( ! function_exists( 'il_field' ) )
{
	function il_field( $data )
	{
		if ( isset( $data ) )
			echo $data;
		else
			echo '';
	}
}