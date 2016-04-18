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
		register_setting( 'google_locations', 'google_locations_settings' );

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

		// Sanitize
		$settings['mode'] 		= isset( $_POST['mode'] ) ? trim( $_POST['mode'] ) : 'basic';
		$settings['app_id'] 	= isset( $_POST['app_id'] ) ? trim( $_POST['app_id'] ) : '';
		$settings['auto_add'] 	= isset( $_POST['auto_add'] ) ? true : false;
		$settings['sdk_locale'] = trim( $_POST['sdk_locale'] );
		
		$settings = apply_filters( 'il_settings_before_update', $settings );
		
		update_option( 'get_facebook_likes', $settings );

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
		?>
		<script type="text/javascript">
		jQuery( function($) {
			$('[name="mode"], #auto_add').change(function (){
				var modeSelected = $('[name="mode"]:checked').val();
				var autoAdd 	 = $('#auto_add').is(':checked');

				if ( modeSelected === 'advanced' ) 
				{
					$('#auto_add_section, #app_id_section').show();

					if (autoAdd) {
						$('#app_id_section, #sdk_locale_section').show();
						$('#setup-guide').hide();
					}
					else {
						$('#app_id_section, #sdk_locale_section').hide();
						$('#setup-guide').show();
					}
				}
				else 
				{
					$('#setup-guide, #auto_add_section, #app_id_section, #sdk_locale_section').hide();
				}
			});

			$('[name="mode"], #auto_add').trigger('change');
		});
		</script>

		<div class="wrap">
			<h2><?php _e( 'Google Locations', 'instant-locations' ); ?></h2>
			
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
														<input type="text" name="componentRestriction[country]" id="country" value="<?php echo il_setting('bar'); ?>" />
													</label>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Enter country name in case you want to get the search results only in that country. For example <code>us</code>. Leaves blank if you not sure.' );
				                    					?>
				                    				</p>
			                    				</div>
			                    			</td>
			                    		</tr>

			                    		<tr valign="top">
			                    			<th><?php _e( 'Types', 'instant-locations' ); ?></th>
			                    			<td>
			                    				<div>
			                    					<label>
														<input type="radio" name="type" value="establishment" /> Geocode
													</label><br>
													<label>
														<input type="radio" name="type" value="establishment" /> Address
													</label><br>
													<label>
														<input type="radio" name="type" value="establishment" /> Establishment
													</label><br>
													<label>
														<input type="radio" name="type" value="establishment" /> Regions
													</label><br>
				                    				<label>
														<input type="radio" name="type" value="establishment" /> Cities
													</label><br>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Specifies an explicit type or a type collection. Select none if you not sure.' );
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
														<input type="text" name="api_key" id="api_key" value="<?php echo il_setting('api_key'); ?>" />
													</label>
				                    				<p class="description">
				                    					<?php 
				                    					_e( 'Enter your API Key. For large application which have many request through Google Locations API. Not required.' );
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
			                  	<h3 class="hndle ui-sortable-handle"><?php _e( 'Fields Settings', 'instant-locations' ); ?></h3>
			                  	<div class="inside">
			                    	<table class="form-table">
			                    		<thead>
			                    			<tr>
			                    				<th>Field</th>
			                    				<th>Show?</th>
			                    				<th>Long Name?</th>
			                    				<th>Title</th>		              
			                    			</tr>
			                    		</thead>
			                    		<tbody>
				                    		<tr>
				                    			<td>administrative_area_level_1</td>
				                    			<td><input type="checkbox"></td>
				                    			<td><input type="checkbox"></td>
				                    			<td>State</td>
				                    		</tr>
			                    		</tbody>
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