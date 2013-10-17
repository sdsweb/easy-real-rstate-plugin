<?php
/**
 * Easy_Real_Estate_Post_Types
 *
 * Description: Setup post types, Registers custom post types, set up custom post meta, save_post action, etc...
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Post_Types' ) ) {
	class Easy_Real_Estate_Post_Types {

		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Post_Types;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
			add_action( 'init', array( $this, 'init' ) ); // Register Custom Post Types (with post meta/taxonomies, etc...)
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) ); // Custom Post Meta Boxes
			add_action( 'save_post', array( $this, 'save_post' ) ); // Save custom post meta
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); // Enqueue custom post meta CSS on admin only
			add_filter( 'template_include', array( $this, 'template_include' ) ); // Load the correct template for display of custom post types
		}

		/**
		 * This function registers all custom post types (with post meta/taxonomies, etc...)
		 */
		function init() {
			// Agents
			register_post_type( 'ere_agents', array(
				'labels' => array(
					'name' => 'Agents',
					'singular_name' => 'Agent',
					'add_new' => 'Add Agent',
					'add_new_item' => 'Add New Agent',
					'edit_item' => 'Edit Agent',
					'new_item' => 'New Agent',
					'all_items' => 'All Agents',
					'view_item' => 'View Agent',
					'search_items' => 'Search Agents',
					'not_found' =>  'No Agents found',
					'not_found_in_trash' => 'No Agents found in Trash',
					'menu_name' => 'Agents'
				),
				'public' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'rewrite' => array( 'slug' => 'agent', 'with_front' => false ),
				'has_archive' => 'agents', // Permalink for viewing archive
				'show_in_nav_menus' => true
			) );

			// Properties
			register_post_type( 'ere_properties', array(
				'labels' => array(
					'name' => 'Properties',
					'singular_name' => 'Property',
					'add_new' => 'Add Property',
					'add_new_item' => 'Add New Property',
					'edit_item' => 'Edit Property',
					'new_item' => 'New Property',
					'all_items' => 'All Properties',
					'view_item' => 'View Property',
					'search_items' => 'Search Properties',
					'not_found' =>  'No Properties found',
					'not_found_in_trash' => 'No Properties found in Trash',
					'menu_name' => 'Properties'
				),
				'public' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'rewrite' => array( 'slug' => 'property', 'with_front' => false ),
				'has_archive' => 'properties', // Permalink for viewing archive
				'show_in_nav_menus' => true
			) );

			// Testimonials
			register_post_type( 'ere_testimonials', array(
				'labels' => array(
					'name' => 'Testimonials',
					'singular_name' => 'Testimonial',
					'add_new' => 'Add New Testimonial',
					'add_new_item' => 'Add New Testimonial',
					'edit_item' => 'Edit Testimonial',
					'new_item' => 'New Testimonial',
					'all_items' => 'All Testimonials',
					'view_item' => 'View Testimonial',
					'search_items' => 'Search Testimonials',
					'not_found' =>  'No testimonial found',
					'not_found_in_trash' => 'No testimonials found in Trash', 
					'parent_item_colon' => '',
					'menu_name' => 'Testimonials',
				),
				'public' => true,
				'capability_type' => 'post',
				'description' => 'A post type to capture and display testimonials about your website, business, or company.',
				'exclude_from_search' => true,
				'supports' => array( 'title', 'editor', 'author', 'excerpt', 'revisions', 'thumbnail' ),
				'rewrite' => array( 'slug' => 'testimonials' ),
				'has_archive' => 'testimonials',  // Permalink for viewing archive
				'hierarchical' => false,
				'show_in_nav_menus' => false
			) );
		}

			/*
			 * This function adds meta box(es) to respected custom post types.
			 * @see: init() for custom post types
			 */
			function add_meta_boxes( $post ) {
				add_meta_box( 'ere_agents_meta', 'Agent Information', array( $this, 'ere_agents_display_meta_box' ), 'ere_agents', 'normal', 'high' ); // Agents
				add_meta_box( 'ere_properties_meta', 'Property Information', array( $this, 'ere_properties_display_meta_box' ), 'ere_properties', 'normal', 'high' ); // Properties
				add_meta_box( 'ere_testimonials_meta', 'Testimonial Credits', array( $this, 'ere_testimonials_display_meta_box' ), 'ere_testimonials', 'side', 'high' ); // Testimonials
			}

				/*
				 * This function is used to display the custom post meta box added from the function above.
				 */
				function ere_agents_display_meta_box() {
					global $post;

					wp_nonce_field( 'ere_agent_meta_save', 'ere_agent_meta_nonce' ); // Nonce for verification
				?>
					<fieldset name="ere_agent_meta">
						<!-- Agent -->
						<div class="ere-section">
							<h4 class="ere-section-label">Agent Information</h4>
							<span class="description ere-section-description">Use this section to specify information for this agent.</span>
							<div class="ere-clear">&nbsp;</div>

							<div class="ere-sub-section">
								<!-- Position -->
								<div class="ere-label-container">
									<label for="ere_agent_meta_position" class="ere-label">
										<strong>Position</strong>
									</label>
									<span class="description ere-description">Use this field to specify a position (e.g. Real Estate Agent or Real Estate Broker).</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_agent_meta_position" id="_ere_agent_meta_position" class="wide ere-input" placeholder="Enter a position here" autocomplete="off" value="<?php echo ( $ere_agent_position = get_post_meta( $post->ID, 'ere_agent_position', true ) ) ? esc_attr( $ere_agent_position ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>

								<!-- Linked In -->
								<div class="ere-label-container">
									<label for="ere_agent_meta_linked_in" class="ere-label">
										<strong>LinkedIn</strong>
									</label>
									<span class="description ere-description">Use this field to specify a LinkedIn Profile link.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_agent_meta_linked_in" id="_ere_agent_meta_linked_in" class="wide ere-input" placeholder="Enter a LinkedIn Profile Link here" autocomplete="off" value="<?php echo ( $ere_agent_linked_in = get_post_meta( $post->ID, 'ere_agent_linked_in', true ) ) ? esc_attr( $ere_agent_linked_in ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>

								<!-- Facebook -->
								<div class="ere-label-container">
									<label for="ere_agent_meta_facebook" class="ere-label">
										<strong>Facebook</strong>
									</label>
									<span class="description ere-description">Use this field to specify a Facebook Profile link.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_agent_meta_facebook" id="_ere_agent_meta_facebook" class="wide ere-input" placeholder="Enter Facebook Profile Link here" autocomplete="off" value="<?php echo ( $ere_agent_facebook = get_post_meta( $post->ID, 'ere_agent_facebook', true ) ) ? esc_attr( $ere_agent_facebook ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>

								<!-- Twitter -->
								<div class="ere-label-container">
									<label for="ere_agent_meta_twitter" class="ere-label">
										<strong>Twitter</strong>
									</label>
									<span class="description ere-description">Use this field to specify a Twitter Profile link.</span>
								</div>
								<div class="ere-input-container ere-last">
									<input type="text" name="ere_agent_meta_twitter" id="_ere_agent_meta_twitter" class="wide ere-input" placeholder="Enter Twitter Profile Link here" autocomplete="off" value="<?php echo ( $ere_agent_twitter = get_post_meta( $post->ID, 'ere_agent_twitter', true ) ) ? esc_attr( $ere_agent_twitter ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>
						</div>
					</fieldset>
				<?php
				}

				/*
				 * This function is used to display the custom post meta box added from the function above.
				 */
				function ere_properties_display_meta_box() {
					global $post;

					wp_nonce_field( 'ere_property_meta_save', 'ere_property_meta_nonce' ); // Nonce for verification
				?>
					<fieldset name="ere_property_meta">
						<!-- Property Information -->
						<div class="ere-section">
							<h4 class="ere-section-label">Property Information</h4>
							<span class="description ere-section-description">Use this section to specify information for this property.</span>
							<div class="ere-clear">&nbsp;</div>

							<div class="ere-sub-section">
								<!-- For Sale/Rent -->
								<div class="ere-label-container">
									<label for="ere_property_meta_status" class="ere-label">
										<strong>For Sale/Rent</strong>
									</label>
									<span class="description ere-description">Use this field to specify whether this property is for sale or for rent.</span>
								</div>
								<div class="ere-input-container">
									<?php $ere_property_status = get_post_meta( $post->ID, 'ere_property_status', true ) ; // Get property status ?>
									<input type="radio" name="ere_property_meta_status" value="sale" <?php checked( $ere_property_status, 'sale' ); ?>> <label for="ere_property_meta_status" class="ere-label">For Sale</label>
									<br />
									<input type="radio" name="ere_property_meta_status" value="rent" <?php checked( $ere_property_status, 'rent' ); ?>> <label for="ere_property_meta_status" class="ere-label">For Rent</label>
									<?php if ( $ere_property_status ) : ?>
										<br />
										<input type="radio" name="ere_property_meta_status" value=""> <label for="ere_property_meta_status" class="ere-label">Clear</label>
									<?php endif; ?>
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Price -->
								<div class="ere-label-container">
									<label for="ere_property_meta_price" class="ere-label">
										<strong>Property Price</strong>
									</label>
									<span class="description ere-description">Use this field to update the price for this property (e.g. $300,000).</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_price" id="ere_property_meta_price" class="wide ere-input" placeholder="Enter a price here" autocomplete="off" value="<?php echo ( $ere_property_price = get_post_meta( $post->ID, 'ere_property_price', true ) ) ? esc_attr( $ere_property_price ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Video URL -->
								<div class="ere-label-container">
									<label for="ere_property_meta_video" class="ere-label">
										<strong>Video URL</strong>
									</label>
									<span class="description ere-description">Use this field to update the video for this property (see <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">here</a> for supported a list of sites)</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_video" id="ere_property_meta_video" class="wide ere-input" placeholder="Enter a video here" autocomplete="off" value="<?php echo ( $ere_property_video = get_post_meta( $post->ID, 'ere_property_video', true ) ) ? esc_attr( $ere_property_video ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Highlight -->
								<div class="ere-label-container">
									<label for="ere_property_meta_highlight" class="ere-label">
										<strong>Highlight</strong>
									</label>
									<span class="description ere-description">Use this field to update the highlight for this property. Accepts the following HTML: &lt;strong&gt;&lt;/strong&gt;.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_highlight" id="ere_property_meta_highlight" class="wide ere-input" placeholder="Enter a highlight here" autocomplete="off" value="<?php echo ( $ere_property_highlight = get_post_meta( $post->ID, 'ere_property_highlight', true ) ) ? esc_attr( $ere_property_highlight ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Soliloquy Slider -->
								<div class="ere-label-container">
									<label for="ere_property_meta_soliloquy" class="ere-label">
										<strong>Soliloquy Slider</strong>
									</label>
									<span class="description ere-description">
										Use this field to add a <a href="http://wordpress.org/extend/plugins/soliloquy-lite/" target="_blank">Soliloquy Slider</a> shortcode to this property (e.g. [soliloquy id="1"]). Dimensions: <strong>685 x 300</strong>.
										<br />
										<strong>Note: Entering a shortcode will display a Soliloquy Slider in place of the featured image when viewing single properties.</strong>
									</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_soliloquy" id="ere_property_meta_soliloquy" class="wide ere-input" placeholder="Enter a soliloquy here" autocomplete="off" value="<?php echo ( $ere_property_soliloquy = get_post_meta( $post->ID, 'ere_property_soliloquy', true ) ) ? esc_attr( $ere_property_soliloquy ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>
						</div>

						<!-- Address -->
						<div class="ere-section">
							<h4 class="ere-section-label">Address</h4>
							<span class="description ere-section-description">Use this section to update the address for this Property.</span>
							<div class="ere-clear">&nbsp;</div>

							<div class="ere-sub-section">
								<!-- Street -->
								<div class="ere-label-container">
									<label for="ere_property_meta_address_street" class="ere-label">
										<strong>Address</strong>
									</label>
									<span class="description ere-description">Use this field to update the address for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_address_street" id="ere_property_meta_address_street" class="wide ere-input" placeholder="Enter a street address here" autocomplete="off" value="<?php echo ( $ere_property_address_street = get_post_meta( $post->ID, 'ere_property_address_street', true ) ) ? esc_attr( $ere_property_address_street ) : false; ?>" />
									<input type="text" name="ere_property_meta_address_street_2" id="ere_property_meta_address_street_2" class="wide ere-input" placeholder="Enter the 2nd street address line here" autocomplete="off" value="<?php echo ( $ere_property_address_street_2 = get_post_meta( $post->ID, 'ere_property_address_street_2', true ) ) ? esc_attr( $ere_property_address_street_2 ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- City/State -->
								<div class="ere-label-container">
									<label for="ere_property_meta_address_city" class="ere-label">
										<strong>City/State</strong>
									</label>
									<span class="description ere-description">Use this field to update city/state for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_address_city" id="ere_property_meta_address_city" class="wide ere-input ere-input-left" placeholder="Enter a city here" autocomplete="off" value="<?php echo ( $ere_property_address_city = get_post_meta( $post->ID, 'ere_property_address_city', true ) ) ? esc_attr( $ere_property_address_city ) : false; ?>" />
									<select name="ere_property_meta_address_state" id="ere_property_meta_address_state">
										<option value="">Select A State</option>
										<?php $ere_property_address_state = get_post_meta( $post->ID, 'ere_property_address_state', true ) ; // Get property state (address) ?>
										<option value="AL" <?php selected( $ere_property_address_state, 'AL' ); ?>>AL</option>
										<option value="AK" <?php selected( $ere_property_address_state, 'AK' ); ?>>AK</option>
										<option value="AZ" <?php selected( $ere_property_address_state, 'AZ' ); ?>>AZ</option>
										<option value="AR" <?php selected( $ere_property_address_state, 'AR' ); ?>>AR</option>
										<option value="CA" <?php selected( $ere_property_address_state, 'CA' ); ?>>CA</option>
										<option value="CO" <?php selected( $ere_property_address_state, 'CO' ); ?>>CO</option>
										<option value="CT" <?php selected( $ere_property_address_state, 'CT' ); ?>>CT</option>
										<option value="DE" <?php selected( $ere_property_address_state, 'DE' ); ?>>DE</option>
										<option value="DC" <?php selected( $ere_property_address_state, 'DC' ); ?>>DC</option>
										<option value="FL" <?php selected( $ere_property_address_state, 'FL' ); ?>>FL</option>
										<option value="GA" <?php selected( $ere_property_address_state, 'GA' ); ?>>GA</option>
										<option value="HI" <?php selected( $ere_property_address_state, 'HI' ); ?>>HI</option>
										<option value="ID" <?php selected( $ere_property_address_state, 'ID' ); ?>>ID</option>
										<option value="IL" <?php selected( $ere_property_address_state, 'IL' ); ?>>IL</option>
										<option value="IN" <?php selected( $ere_property_address_state, 'IN' ); ?>>IN</option>
										<option value="IA" <?php selected( $ere_property_address_state, 'IA' ); ?>>IA</option>
										<option value="KS" <?php selected( $ere_property_address_state, 'KS' ); ?>>KS</option>
										<option value="KY" <?php selected( $ere_property_address_state, 'KY' ); ?>>KY</option>
										<option value="LA" <?php selected( $ere_property_address_state, 'LA' ); ?>>LA</option>
										<option value="ME" <?php selected( $ere_property_address_state, 'ME' ); ?>>ME</option>
										<option value="MD" <?php selected( $ere_property_address_state, 'MD' ); ?>>MD</option>
										<option value="MA" <?php selected( $ere_property_address_state, 'MA' ); ?>>MA</option>
										<option value="MI" <?php selected( $ere_property_address_state, 'MI' ); ?>>MI</option>
										<option value="MN" <?php selected( $ere_property_address_state, 'MN' ); ?>>MN</option>
										<option value="MS" <?php selected( $ere_property_address_state, 'MS' ); ?>>MS</option>
										<option value="MO" <?php selected( $ere_property_address_state, 'MO' ); ?>>MO</option>
										<option value="MT" <?php selected( $ere_property_address_state, 'MT' ); ?>>MT</option>
										<option value="NE" <?php selected( $ere_property_address_state, 'NE' ); ?>>NE</option>
										<option value="NV" <?php selected( $ere_property_address_state, 'NV' ); ?>>NV</option>
										<option value="NH" <?php selected( $ere_property_address_state, 'NH' ); ?>>NH</option>
										<option value="NJ" <?php selected( $ere_property_address_state, 'NJ' ); ?>>NJ</option>
										<option value="NM" <?php selected( $ere_property_address_state, 'NM' ); ?>>NM</option>
										<option value="NY" <?php selected( $ere_property_address_state, 'NY' ); ?>>NY</option>
										<option value="NC" <?php selected( $ere_property_address_state, 'NC' ); ?>>NC</option>
										<option value="ND" <?php selected( $ere_property_address_state, 'ND' ); ?>>ND</option>
										<option value="OH" <?php selected( $ere_property_address_state, 'OH' ); ?>>OH</option>
										<option value="OK" <?php selected( $ere_property_address_state, 'OK' ); ?>>OK</option>
										<option value="OR" <?php selected( $ere_property_address_state, 'OR' ); ?>>OR</option>
										<option value="PA" <?php selected( $ere_property_address_state, 'PA' ); ?>>PA</option>
										<option value="RI" <?php selected( $ere_property_address_state, 'RI' ); ?>>RI</option>
										<option value="SC" <?php selected( $ere_property_address_state, 'SC' ); ?>>SC</option>
										<option value="SD" <?php selected( $ere_property_address_state, 'SD' ); ?>>SD</option>
										<option value="TN" <?php selected( $ere_property_address_state, 'TN' ); ?>>TN</option>
										<option value="TX" <?php selected( $ere_property_address_state, 'TX' ); ?>>TX</option>
										<option value="UT" <?php selected( $ere_property_address_state, 'UT' ); ?>>UT</option>
										<option value="VT" <?php selected( $ere_property_address_state, 'VT' ); ?>>VT</option>
										<option value="VA" <?php selected( $ere_property_address_state, 'VA' ); ?>>VA</option>
										<option value="WA" <?php selected( $ere_property_address_state, 'WA' ); ?>>WA</option>
										<option value="WV" <?php selected( $ere_property_address_state, 'WV' ); ?>>WV</option>
										<option value="WI" <?php selected( $ere_property_address_state, 'WI' ); ?>>WI</option>
										<option value="WY" <?php selected( $ere_property_address_state, 'WY' ); ?>>WY</option>
									</select>
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Zip Code -->
								<div class="ere-label-container">
									<label for="ere_property_meta_address_zip_code" class="ere-label">
										<strong>Zip Code</strong>
									</label>
									<span class="description ere-description">Use this field to update the city for this property.</span>
								</div>
								<div class="ere-input-container ere-last">
									<input type="text" name="ere_property_meta_address_zip_code" id="ere_property_meta_address_zip_code" class="wide ere-input" placeholder="Enter a zip code here" autocomplete="off" value="<?php echo ( $ere_property_address_zip_code = get_post_meta( $post->ID, 'ere_property_address_zip_code', true ) ) ? esc_attr( $ere_property_address_zip_code ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Lat/Long Input -->
								<div class="ere-label-container">
									<label for="ere_property_meta_address_lat_long" class="ere-label">
										<strong>Latitude/Longitude (Optional)</strong>
									</label>
									<span class="description ere-description">Use this field <strong>ONLY</strong> if the map displayed on the property page is incorrect. (e.g. 41.574855,-71.003737)</span>
								</div>
								<div class="ere-input-container ere-last">
									<input type="text" name="ere_property_meta_address_lat_long" id="ere_property_meta_address_lat_long" class="wide ere-input" placeholder="Enter a lat/long query here" autocomplete="off" value="<?php echo ( $ere_property_address_lat_long = get_post_meta( $post->ID, 'ere_property_address_lat_long', true ) ) ? esc_attr( $ere_property_address_lat_long ) : false; ?>" />
								</div>
								
								<div class="ere-clear">&nbsp;</div>
							</div>
						</div>

						
						<!-- Other Property Information -->
						<div class="ere-section">
							<h4 class="ere-section-label">Other Property Information</h4>
							<span class="description ere-section-description">Use this section to specify other information for this property such as # of baths, square footage, etc...</span>
							<div class="ere-clear">&nbsp;</div>

							<div class="ere-sub-section">
								<!-- MLS # -->
								<div class="ere-label-container">
									<label for="ere_property_metamls" class="ere-label">
										<strong>MLS #</strong>
									</label>
									<span class="description ere-description">Use this field to specify an MLS number for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_mls" id="ere_property_meta_mls" class="wide ere-input" placeholder="Enter an MLS number here" autocomplete="off" value="<?php echo ( $ere_property_mls = get_post_meta( $post->ID, 'ere_property_mls', true ) ) ? esc_attr( $ere_property_mls ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Square Feet -->
								<div class="ere-label-container">
									<label for="ere_property_metasquare_feet" class="ere-label">
										<strong>Square Feet</strong>
									</label>
									<span class="description ere-description">Use this field to specify a square footage for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_square_feet" id="ere_property_meta_square_feet" class="wide ere-input" placeholder="Enter square footage here" autocomplete="off" value="<?php echo ( $ere_property_square_feet = get_post_meta( $post->ID, 'ere_property_square_feet', true ) ) ? esc_attr( $ere_property_square_feet ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Bedrooms -->
								<div class="ere-label-container">
									<label for="ere_property_metabedrooms" class="ere-label">
										<strong>Bedrooms</strong>
									</label>
									<span class="description ere-description">Use this field to specify a number of bedrooms for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_bedrooms" id="ere_property_meta_bedrooms" class="wide ere-input" placeholder="Enter a number of bedrooms here" autocomplete="off" value="<?php echo ( $ere_property_bedrooms = get_post_meta( $post->ID, 'ere_property_bedrooms', true ) ) ? esc_attr( $ere_property_bedrooms ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Bathrooms -->
								<div class="ere-label-container">
									<label for="ere_property_metabathrooms" class="ere-label">
										<strong>Bathrooms</strong>
									</label>
									<span class="description ere-description">Use this field to specify a number of bathrooms for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_bathrooms" id="ere_property_meta_bathrooms" class="wide ere-input" placeholder="Enter a number of bathrooms here" autocomplete="off" value="<?php echo ( $ere_property_bathrooms = get_post_meta( $post->ID, 'ere_property_bathrooms', true ) ) ? esc_attr( $ere_property_bathrooms ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>

							<div class="ere-sub-section">
								<!-- Basement -->
								<div class="ere-label-container">
									<label for="ere_property_metabasement" class="ere-label">
										<strong>Basement</strong>
									</label>
									<span class="description ere-description">Use this field to specify a basement type for this property.</span>
								</div>
								<div class="ere-input-container">
									<input type="text" name="ere_property_meta_basement" id="ere_property_meta_basement" class="wide ere-input" placeholder="Enter a basement type here" autocomplete="off" value="<?php echo ( $ere_property_basement = get_post_meta( $post->ID, 'ere_property_basement', true ) ) ? esc_attr( $ere_property_basement ) : false; ?>" />
								</div>

								<div class="ere-clear">&nbsp;</div>
							</div>
						</div>
					</fieldset>
				<?php
				}


				/*
				 * This function is used to display the custom post meta box added from the function above.
				 */
				function ere_testimonials_display_meta_box() {
					global $post;

					wp_nonce_field( 'ere_testimonial_meta_save', 'ere_testimonial_meta_nonce' );

					// Fetch current post meta (if any)
					if( ! $current_credit = get_post_meta( $post->ID, '_sbt_testimonial_credits', true ) )
						$current_credit = array();
					?>
					<fieldset name="_sbt_testimonial_credits">
						<p>
							<label for="_sbt_testimonial_name"><strong>Person's Name</strong></label>
							<input type="text" name="_sbt_testimonial_name" id="_sbt_testimonial_name" class="wide" value="<?php echo ( empty( $current_credit['name'] ) ) ? '' : esc_attr( $current_credit['name'] ); ?>" />
						</p>
						<p>
							<label for="_sbt_testimonial_position"><strong>Person's Position</strong></label>
							<input type="text" name="_sbt_testimonial_position" id="_sbt_testimonial_position" class="wide" value="<?php echo ( empty( $current_credit['position'] ) ) ? '' : esc_attr( $current_credit['position'] ); ?>" />
						</p>
						<p>
							<label for="_sbt_testimonial_company"><strong>Person's Company</strong></label>
							<input type="text" name="_sbt_testimonial_company" id="_sbt_testimonial_company" class="wide" value="<?php echo ( empty( $current_credit['company'] ) ) ? '' : esc_attr( $current_credit['company'] ); ?>" />
						</p>
					</fieldset>
				<?php
				}

		/*
		 * This function handles saving the custom post meta from the meta boxes.
		 */
		function save_post( $post_id ) {
			global $post;
			// Auto-saving, proceed as normal
			if ( wp_is_post_revision( $post_id ) || defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;

			/*
			 * Agents
			 */

			// Save custom post meta on Agents
			if ( isset( $_POST['ere_agent_meta_nonce'] ) && wp_verify_nonce( $_POST['ere_agent_meta_nonce'], 'ere_agent_meta_save' ) ) {
				// Position
				if ( ! empty( $_POST['ere_agent_meta_position'] ) )
					update_post_meta( $post_id, 'ere_agent_position', sanitize_text_field( $_POST['ere_agent_meta_position'] ) );
				else
					delete_post_meta( $post_id, 'ere_agent_position' );

				// Social Media - Linked In
				if ( ! empty( $_POST['ere_agent_meta_linked_in'] ) )
					update_post_meta( $post_id, 'ere_agent_linked_in', esc_url( $_POST['ere_agent_meta_linked_in'] ) );
				else
					delete_post_meta( $post_id, 'ere_agent_linked_in' );

				// Social Media - Facebook
				if ( ! empty( $_POST['ere_agent_meta_facebook'] ) )
					update_post_meta( $post_id, 'ere_agent_facebook', esc_url( $_POST['ere_agent_meta_facebook'] ) );
				else
					delete_post_meta( $post_id, 'ere_agent_facebook' );

				// Social Media - Twitter
				if ( ! empty( $_POST['ere_agent_meta_twitter'] ) )
					update_post_meta( $post_id, 'ere_agent_twitter', esc_url( $_POST['ere_agent_meta_twitter'] ) );
				else
					delete_post_meta( $post_id, 'ere_agent_twitter' );
			}


			/*
			 * Properties
			 */

			// Save custom post meta on Properties
			if ( isset( $_POST['ere_property_meta_nonce'] ) && wp_verify_nonce( $_POST['ere_property_meta_nonce'], 'ere_property_meta_save' ) ) {
				// Status
				if ( ! empty( $_POST['ere_property_meta_status'] ) )
					update_post_meta( $post_id, 'ere_property_status', sanitize_text_field( $_POST['ere_property_meta_status'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_status' );

				// Price
				if ( ! empty( $_POST['ere_property_meta_price'] ) )
					update_post_meta( $post_id, 'ere_property_price', sanitize_text_field( $_POST['ere_property_meta_price'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_price' );

				// Video URL
				if ( ! empty( $_POST['ere_property_meta_video'] ) )
					update_post_meta( $post_id, 'ere_property_video', esc_url( $_POST['ere_property_meta_video'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_video' );

				// Soliloquy Shortcode (check for valid shortcode)
				if ( ! empty( $_POST['ere_property_meta_soliloquy'] ) ) {
					$shortcode_pattern = '/' . get_shortcode_regex() . '/s';

					// Check to make sure we have a valid soliloquy shortcode
					if ( preg_match_all( $shortcode_pattern, $_POST['ere_property_meta_soliloquy'], $matches ) && array_key_exists( 2, $matches ) && in_array( 'soliloquy', $matches[2] ) )
						update_post_meta( $post_id, 'ere_property_soliloquy', sanitize_text_field( $_POST['ere_property_meta_soliloquy'] ) );
					else
						delete_post_meta( $post_id, 'ere_property_soliloquy' );
				}
				else
					delete_post_meta( $post_id, 'ere_property_soliloquy' );

				// Highlight
				if ( ! empty( $_POST['ere_property_meta_highlight'] ) )
					update_post_meta( $post_id, 'ere_property_highlight', wp_kses( $_POST['ere_property_meta_highlight'], array( 'strong' => array() ) ) );
				else
					delete_post_meta( $post_id, 'ere_property_highlight' );


				// Address (Street)
				if ( ! empty( $_POST['ere_property_meta_address_street'] ) )
					update_post_meta( $post_id, 'ere_property_address_street', sanitize_text_field( $_POST['ere_property_meta_address_street'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_street' );

				// Address (Street 2)
				if ( ! empty( $_POST['ere_property_meta_address_street_2'] ) )
					update_post_meta( $post_id, 'ere_property_address_street_2', sanitize_text_field( $_POST['ere_property_meta_address_street_2'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_street_2' );

				// Address (City)
				if ( ! empty( $_POST['ere_property_meta_address_city'] ) )
					update_post_meta( $post_id, 'ere_property_address_city', sanitize_text_field( $_POST['ere_property_meta_address_city'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_city' );

				// Address (State)
				if ( ! empty( $_POST['ere_property_meta_address_state'] ) )
					update_post_meta( $post_id, 'ere_property_address_state', sanitize_text_field( $_POST['ere_property_meta_address_state'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_state' );

				// Address (Zip Code)
				if ( ! empty( $_POST['ere_property_meta_address_zip_code'] ) )
					update_post_meta( $post_id, 'ere_property_address_zip_code', sanitize_text_field( $_POST['ere_property_meta_address_zip_code'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_zip_code' );

				// Address (Lat/Long)
				if ( ! empty( $_POST['ere_property_meta_address_lat_long'] ) )
					update_post_meta( $post_id, 'ere_property_address_lat_long', sanitize_text_field( $_POST['ere_property_meta_address_lat_long'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_address_lat_long' );


				// MLS #
				if ( ! empty( $_POST['ere_property_meta_mls'] ) )
					update_post_meta( $post_id, 'ere_property_mls', sanitize_text_field( $_POST['ere_property_meta_mls'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_mls' );

				// Square Feet
				if ( ! empty( $_POST['ere_property_meta_square_feet'] ) )
					update_post_meta( $post_id, 'ere_property_square_feet', sanitize_text_field( $_POST['ere_property_meta_square_feet'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_square_feet' );

				// Bedrooms
				if ( ! empty( $_POST['ere_property_meta_bedrooms'] ) )
					update_post_meta( $post_id, 'ere_property_bedrooms', sanitize_text_field( $_POST['ere_property_meta_bedrooms'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_bedrooms' );

				// Bathrooms
				if ( ! empty( $_POST['ere_property_meta_bathrooms'] ) )
					update_post_meta( $post_id, 'ere_property_bathrooms', sanitize_text_field( $_POST['ere_property_meta_bathrooms'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_bathrooms' );

				// Basement
				if ( ! empty( $_POST['ere_property_meta_basement'] ) )
					update_post_meta( $post_id, 'ere_property_basement', sanitize_text_field( $_POST['ere_property_meta_basement'] ) );
				else
					delete_post_meta( $post_id, 'ere_property_basement' );
			}


			/*
			 * Testimonials
			 */

			// Save custom post meta on Testimonials
			if ( isset( $_POST['ere_testimonial_meta_nonce'] ) && wp_verify_nonce( $_POST['ere_testimonial_meta_nonce'], 'ere_testimonial_meta_save' ) ) {
				$new_credits = array();
				
				$new_credits['name'] = ( empty( $_POST['_sbt_testimonial_name'] ) ) ? '' : sanitize_text_field( $_POST['_sbt_testimonial_name'] );
				$new_credits['position'] = ( empty( $_POST['_sbt_testimonial_position'] ) ) ? '' : sanitize_text_field( $_POST['_sbt_testimonial_position'] );
				$new_credits['company'] = ( empty( $_POST['_sbt_testimonial_company'] ) ) ? '' : sanitize_text_field( $_POST['_sbt_testimonial_company'] );
				
				if( empty( $new_credits ) )
					delete_post_meta( $post_id, '_sbt_testimonial_credits' );
				else
					update_post_meta( $post_id, '_sbt_testimonial_credits', $new_credits );
			}
		}

		/*
		 * This function enqueues all scripts and styles for custom post types
		 */
		function admin_enqueue_scripts( $hook ) {
			global $post;

			// Enqueue custom meta CSS on Agents and Properties only
			if ( ! empty( $post ) && ( $post->post_type == 'ere_agents' || $post->post_type == 'ere_properties' ) )
				wp_enqueue_style( 'ere-admin-meta', ERE_PLUGIN_URL . 'includes/post-types/css/ere-meta.css', false, ERE_VERSION ); // Easy Real Estate Admin Meta
		}


		/*
		 * This function determines the correct template to load, first checking to see if the current theme (or parent theme) has the template file
		 */
		function template_include( $template ) {
			global $wp_query, $post;

			// Taxonomy
			if ( is_tax() && ! locate_template( 'taxonomy.php' ) )
					$template = ERE_PLUGIN_DIR . 'templates/taxonomy.php';

			// Agents - Archive
			if ( is_post_type_archive( 'ere_agents' ) && ! locate_template( 'archive-ere_agents.php' ) )
					$template = ERE_PLUGIN_DIR . 'templates/archive-ere_agents.php';
			// Agents - Single
			if ( get_post_type() === 'ere_agents' && is_single() && ! locate_template( 'single-ere_agents.php' ) )
					$template = ERE_PLUGIN_DIR . 'templates/single-ere_agents.php';


			// Properties - Archive
			if ( is_post_type_archive( 'ere_properties' ) && ! locate_template( 'archive-ere_properties.php' ) )
					$template = ERE_PLUGIN_DIR . 'templates/archive-ere_properties.php';
			// Properties - Single
			if ( get_post_type() === 'ere_properties' && is_single() && ! locate_template( 'single-ere_properties.php' ) )
					$template = ERE_PLUGIN_DIR . 'templates/single-ere_properties.php';

			return $template;
		}
	}


	function Easy_Real_Estate_Post_Types_Instance() {
		return Easy_Real_Estate_Post_Types::instance();
	}

	Easy_Real_Estate_Post_Types_Instance();
}