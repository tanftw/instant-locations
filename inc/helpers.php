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

		'api_key' => 'YOUR API KEY HERE',
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

/**
 * Set Location data for an Object
 * 
 * @param  Int $object_id   Object id
 * @param  Mixed $address_component Address component. 
 *         If it's a key => value. Then forget $value param.
 * @param  string $value Value to set.
 * @param  string $object_type Object type. Supports `post`, `user`, `comment`
 * 
 * @return bool Update status
 */
function il_set_data( $object_id, $address_component, $value = '', $object_type = 'post' )
{
	global $wpdb;

	$object_id = intval( $object_id );

	// If address component is the string of column name. Change it to array
	if ( ! is_array( $address_component ) && ! empty( $value ) )
		$address_component = array($address_component => $value);

	// Set `geometry` column value for users who need it 
	if ( isset( $address_component['lat'] ) && isset( $address_component['lng'] ) )
		$address_component['geometry'] = $address_component['lat'] . ',' . $address_component['lng'];

	// Check if has data. Because we won't use $wpdb->replace() method
	$has_data = $wpdb->get_var( $wpdb->prepare( "
		SELECT 1 
		FROM {$wpdb->prefix}locations 
		WHERE object_id = %d
		AND object_type = %s",
		$object_id,
		$object_type
	) );

	// If record already been added. Then update it.
	if ($has_data)
		return $wpdb->update( $wpdb->prefix . 'locations', $address_component, compact( 'object_id', 'object_type' ) );
	
	// Otherwise. Just add it.
	$address_component['object_id'] 	= $object_id;
	$address_component['object_type'] 	= $object_type;

	return $wpdb->insert( $wpdb->prefix . 'locations', $address_component);
}

/**
 * Get location data of an object
 * 
 * @param  Int $object_id  An object id
 * @param  Mixed $address_component Address component to get data. If it's string, then return column value. Otherwise, return whole record.
 * @param  string $object_type  Object type. Default `post`
 * 
 * @return Array Location data
 */
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


/**
 * Print data if isset or print empty string
 * 
 * @param  String $data String to print
 * 
 * @return void
 */
function il_field( $data )
{
	if ( isset( $data ) )
		echo $data;
	else
		echo '';
}
