<?php
/**
 * Settings Page for Instant Locations
 *
 * @author Tan Nguyen <tan@binaty.org>
 */
class IL_Settings
{
	/**
	 * Constructor only to define hooks
	 *
	 * @return void
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Create admin menu under Settings
	 * 
	 * @return void
	 */
	public function admin_menu()
	{
		add_options_page( 
			__( 'Instant Locations', 'instant-locations' ), 
			__( 'Instant Locations', 'instant-locations' ), 
			'manage_options', 
			'instant-locations', 
			array( $this, 'admin_page' ) 
		);
	}

	/**
	 * All plugin settings saved in this method
	 * 
	 * @return Redirect
	 */
	public function admin_init()
	{
		register_setting( 'instant_locations', 'instant_locations_settings' );

		if ( ! isset( $_POST['_page_now'] ) || $_POST['_page_now'] != 'instant-locations' )
			return;

		$settings = array();
		$defaults = il_default_settings();

		foreach ( $defaults as $key => $value )
		{
			$settings[$key] = $value;

			if ( isset( $_POST[$key] ) )
				$settings[$key] = $_POST[$key];
		}
		
		$settings = apply_filters( 'il_settings_before_update', $settings );

		update_option( 'instant_locations', $settings );

		// Redirect with success message
		$_POST['_wp_http_referer'] = add_query_arg( 'success', 'true', $_POST['_wp_http_referer'] );
		wp_redirect( $_POST['_wp_http_referer'] );
		exit;
	}

	/**
	 * Render Settings Page Content
	 * 
	 * @return void
	 */
	public function admin_page()
	{
		$data = il_setting();

		$geo_config_types = array('geocode', 'address', 'establishment', '(cities)', '(regions)');
		?>

		<div class="wrap">
			<h2><?php _e( 'Instant Locations', 'instant-locations' ); ?></h2>
			
			<?php 
			// Display success message when settings saved
			if ( isset( $_GET['success'] ) ) : ?>
			<div id="message" class="updated notice is-dismissible">
				<p><?php _e( 'Settings <strong>saved</strong>.', 'instant-locations' ); ?></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>
			<?php endif; ?>
			
			<form action="options.php" method="post" id="poststuff">
				<?php settings_fields( 'instant-locations' ); ?>
				<div id="post-body" class="metabox-holder columns-2">
					
					<div id="postbox-container-2" class="postbox-container">
						
						<div class="meta-box-sortables">

							<?php do_action( 'il_before_general_settings' ); ?>

							<div class="postbox">
			                	<div class="handlediv" title="Click to toggle"> <br></div>
			                  	<h3 class="hndle ui-sortable-handle"><?php _e( 'Auto Complete Settings', 'instant-locations' ); ?></h3>
			                  	<div class="inside">
			                    	<table class="form-table">
			               
			                    		<tr valign="top">
			                    			<th><?php _e( 'Country', 'instant-locations' ); ?></th>
			                    			<td>
			                    				<div>
			                    					
				                    				<label>
														<input type="text" name="geo_config[componentRestrictions][country]" id="country" value="<?php il_field($data['geo_config']['componentRestrictions']['country']); ?>" />
													</label>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Enter country name in case you want to get the search results only in that country. For example <code>us</code>. Leaves blank to search in all countries.' );
				                    					?>
				                    				</p>
			                    				</div>
			                    			</td>
			                    		</tr>

			                    		<tr valign="top">
			                    			<th><?php _e( 'Types', 'instant-locations' ); ?></th>
			                    			<td>
			                    				<div>
			                    					<?php foreach ($geo_config_types as $type) : ?>
			                    					<label>
														<input type="checkbox" name="geo_config[types][]" value="<?php echo $type ?>" <?php if ( ! empty($data['geo_config']['types'] ) && in_array( $type, (array) $data['geo_config']['types'] ) ) echo 'checked'; ?> /> <?php echo $type ?>
													</label><br>
													<?php endforeach; ?>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Specifies an explicit type or a type collection. Select none if you not sure.', 'instant-locations' );
				                    					?>
				                    				</p>
			                    				</div>
			                    			</td>
			                    		</tr>
			                    	</table>
			                  	</div><!--.inside-->
			              	</div><!--.postbox-->

			              	<div class="postbox">
			                	<div class="handlediv" title="Click to toggle"> <br></div>
			                  	<h3 class="hndle ui-sortable-handle"><?php _e( 'Google Maps Settings', 'instant-locations' ); ?></h3>
			                  	<div class="inside">
			                    	<table class="form-table">
			                    		<tr valign="top">
			                    			<th><?php _e( 'API Key', 'instant-locations' ); ?></th>
			                    			<td>
			                    				<div>

				                    				<label>
														<input type="text" name="api_key" id="api_key" value="<?php il_field($data['api_key']); ?>" />
													</label>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Enter your API Key. For large application which have many requests through Google Locations API. Not required.', 'instant-locations' );
				                    					?>
				                    				</p>
			                    				</div>
			                    			</td>
			                    		</tr>
			                    	</table>
			                  	</div><!--.inside-->
			              	</div><!--.postbox-->

			              	<?php do_action( 'il_after_general_settings' ); ?>

						</div><!--.metaboxes-->

					</div><!--.postbox-container2-->

				</div><!--#post-body-->
				<br class="clear">

				<input type="hidden" name="_page_now" value="instant-locations">
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

new iL_Settings;