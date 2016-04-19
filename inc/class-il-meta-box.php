<?php
/**
 * Register and Processing a Instant Locations meta box in post editing screen 
 *
 * @author Tan Nguyen <tan@binaty.org>
 */
class IL_Meta_Box
{
	public function __construct()
	{
		if ( is_admin() )
		{
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
	}

	/**
     * Meta box initialization.
     */
    public function init_metabox() 
    {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  ) );

        add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
    }
 
    /**
     * Adds the meta box.
     */
    public function add_metabox()
    {
    	// Add Instant Locations meta box
        add_meta_box(
            'instant-locations',
            '<span class="dashicons dashicons-location-alt"></span> ' . __( 'Instant Locations', 'instant-locations' ),
            array( $this, 'render_metabox' ),
            'post',
            'normal',
            'default'
        );
    }
 
    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) 
    {
    	// Get location data if exists
    	$location = il_get_data( $post->ID );
    	?>
    	<div class="form-group row" id="form-group-address">
			<input type="text" id="address" name="location[address]" value="<?php il_field( $location['address'] ); ?>" placeholder="Type an address to start auto populate...">
		</div>
		
		<div class="form-group row">
			<label class="form-label" for="country">Country</label>
			<input type="text" name="location[country]" value="<?php il_field( $location['country'] ); ?>" class="form-control" id="country">
		</div>

		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="administrative_area_level_1">Administrative Area Level 1</label>
				<input type="text" class="form-control" id="administrative_area_level_1" name="location[administrative_area_level_1]" value="<?php il_field( $location['administrative_area_level_1'] ); ?>">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_2">Administrative Area Level 2</label>
				<input type="text" class="form-control" id="administrative_area_level_2" name="location[administrative_area_level_2]" value="<?php il_field( $location['administrative_area_level_2'] ); ?>">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_3">Administrative Area Level 3</label>
				<input type="text" class="form-control" id="administrative_area_level_3" name="location[administrative_area_level_3]" value="<?php il_field( $location['administrative_area_level_3'] ); ?>">
			</div>
		
			<div class="column">
				<label class="form-label" for="administrative_area_level_4">Administrative Area Level 4</label>
				<input type="text" class="form-control" id="administrative_area_level_4" name="location[administrative_area_level_4]" value="<?php il_field( $location['administrative_area_level_4'] ); ?>">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_5">Administrative Area Level 5</label>
				<input type="text" class="form-control" id="administrative_area_level_5" name="location[administrative_area_level_5]" value="<?php il_field( $location['administrative_area_level_5'] ); ?>">
			</div>
		</div>

		<div class="form-group row">
			<label class="form-label" for="postal_code">Postal Code</label>
			<input type="number" class="form-control" id="postal_code" name="location[postal_code]" value="<?php il_field( $location['postal_code'] ); ?>">
		</div>
		
		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="lat">Latitude</label>
				<input type="text" class="form-control" id="lat" name="location[lat]" value="<?php il_field( $location['lat'] ); ?>">
			</div>

			<div class="column">
				<label class="form-label" for="lng">Longitude</label>
				<input type="text" class="form-control" id="lng" name="location[lng]" value="<?php il_field( $location['lng'] ); ?>">
			</div>
		</div>
		
		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="street_number">Street Number</label>
				<input type="text" class="form-control" id="street_number" name="location[street_number]" value="<?php il_field( $location['street_number'] ); ?>">
			</div>

			<div class="column">
				<label class="form-label" for="route">Route</label>
				<input type="text" class="form-control" id="route" name="location[route]" value="<?php il_field( $location['route'] ); ?>">
			</div>
		</div>

		<div class="form-group row">
			<label class="form-label" for="political">Political</label>
			<input type="text" class="form-control" id="political" name="location[political]" value="<?php il_field( $location['political'] ); ?>">
		</div>

		<div class="form-group row">
			<label class="form-label" for="intersection">Intersection</label>
			<input type="text" class="form-control" id="intersection" name="location[intersection]" value="<?php il_field( $location['intersection'] ); ?>">
		</div>

		<?php
		// These fields belows are hidden field. We need to pass data to it before saving to database
		$hidden_fields = array('street_address', 'colloquial_area', 'locality', 'sublocality', 'ward', 'neighborhood',
			'premise', 'subpremise', 'natural_feature', 'airport', 'park', 'point_of_interest', 'post_box', 'floor',
			'room', 'formatted_address', 'location_id', 'url');

		foreach ( $hidden_fields as $field) : ?>
			<input type="hidden" class="form-control" name="location[<?php echo $field ?>]" id="<?php echo $field ?>" value="<?php il_field( $location[$field] ); ?>">
		<?php endforeach; 

        // Add nonce for security and authentication.
        wp_nonce_field( 'instant_locations', 'instant_locations_nonce_field' );
    }
 
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['instant_locations_nonce_field'] ) ? $_POST['instant_locations_nonce_field'] : '';
        $nonce_action = 'instant_locations';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) || ! wp_verify_nonce( $nonce_name, $nonce_action ) )
            return;
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
            return;

        if ( ! empty( $_POST['location'] ) )
        	il_set_data( $post_id, $_POST['location'] );
    }
}

new IL_Meta_Box;