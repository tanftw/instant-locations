<?php

class GL_Meta_Box
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
        add_meta_box(
            'google-locations',
            __( 'Google Locations', 'textdomain' ),
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
    	?>
    	<div class="form-group row" id="form-group-address">
			<input type="text" id="address" name="address" placeholder="Type an address to start auto populate...">
		</div>
		
		<div class="form-group row">
			<label class="form-label" for="country">Country</label>
			<input type="text" name="country" class="form-control" id="country">
		</div>

		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="administrative_area_level_1">Administrative Area Level 1</label>
				<input type="text" class="form-control" id="administrative_area_level_1">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_2">Administrative Area Level 2</label>
				<input type="text" class="form-control" id="administrative_area_level_2">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_3">Administrative Area Level 3</label>
				<input type="text" class="form-control" id="administrative_area_level_3">
			</div>
		</div>

		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="administrative_area_level_4">Administrative Area Level 4</label>
				<input type="text" class="form-control" id="administrative_area_level_4">
			</div>

			<div class="column">
				<label class="form-label" for="administrative_area_level_5">Administrative Area Level 5</label>
				<input type="text" class="form-control" id="administrative_area_level_5">
			</div>
		</div>

		<div class="form-group row">
			<label class="form-label" for="postal_code">Postal Code</label>
			<input type="number" class="form-control" id="postal_code">
		</div>
		
		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="lat">Latitude</label>
				<input type="text" class="form-control" id="lat">
			</div>

			<div class="column">
				<label class="form-label" for="lng">Longitude</label>
				<input type="text" class="form-control" id="lng">
			</div>
		</div>
		
		<div class="form-group row">
			<div class="column">
				<label class="form-label" for="street_number">Street Number</label>
				<input type="text" class="form-control" id="street_number">
			</div>

			<div class="column">
				<label class="form-label" for="route">Route</label>
				<input type="text" class="form-control" id="route">
			</div>
		</div>

		<div class="form-group row">
			<label class="form-label" for="political">Political</label>
			<input type="text" class="form-control" id="political">
		</div>

		<div class="form-group row">
			<label class="form-label" for="intersection">Intersection</label>
			<input type="text" class="form-control" id="intersection">
		</div>

		<?php
        // Add nonce for security and authentication.
        wp_nonce_field( 'google_locations', 'google_locations_nonce_field' );
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
        $nonce_name   = isset( $_POST['google_locations_nonce_field'] ) ? $_POST['google_locations_nonce_field'] : '';
        $nonce_action = 'google_locations';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) )
            return;
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
            return;
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        p($_POST);
    }
}

new GL_Meta_Box;