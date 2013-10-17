<?php
/**
 * Easy_Real_Estate_Property_Taxonomies
 *
 * Description: Allows creation, editing, and deletion of taxonomies and terms for the Properties post type and also creates setting page under Properties post type.
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Property_Taxonomies' ) ) {
	class Easy_Real_Estate_Property_Taxonomies {

		private static $instance; // Keep track of the instance
		public static $ere_property_settings_field = 'ere_property_taxonomies'; // Property taxonomy settings name
		public static $ere_property_post_type = 'ere_properties'; // Property post type id
		public static $ere_property_menu_page = 'ere-register-taxonomies'; // Property menu page slug

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Property_Taxonomies;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Register settings and actions for taxonomies including creation, editing, deleting, etc...
			add_action( 'admin_menu', array( $this, 'admin_menu' ) ); // Add settings sub-menu page
			add_action( 'admin_notices', array( $this, 'admin_notices' ) ); // Output admin notices based on REQUEST parameters

			add_action( 'init', array( &$this, 'init' ) ); // Register taxonomies
		}

		/**
		 * This function registers a setting to store taxonomy data and contains functionality for actions including creation, editing, deleting, etc...
		 */
		function admin_init() {
			// Settings/Options
			register_setting( self::$ere_property_settings_field, self::$ere_property_settings_field );
			add_option( self::$ere_property_settings_field, array(), false, 'yes' ); // Taxonomy option
			add_option( self::$ere_property_settings_field . '_use_default', true, false, 'yes' ); // Default taxonomy option

			// Taxonomy Actions
			if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != self::$ere_property_menu_page ) // Ignore on all pages other than our menu page
				return;
			// Taxonomy creation
			if ( isset( $_REQUEST['action'] ) && 'create' == $_REQUEST['action'] )
				self::update_taxonomy( $_POST['ere_taxonomy'] );
			// Taxonomy editing
			if ( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] )
				self::update_taxonomy( $_POST['ere_taxonomy'], true );
			// Taxonomy deletion
			if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] )
				self::delete_taxonomy( $_REQUEST['id'] );
		}

		/**
		 * This function adds a settings sub-menu page to allow registration of taxonomies.
		 */
		function admin_menu() {
			add_submenu_page( 'edit.php?post_type=' . self::$ere_property_post_type, 'Register Taxonomies', 'Register Taxonomies', 'manage_options', self::$ere_property_menu_page, array( $this, 'ere_register_taxonomy_page_display' ) );
		}

			/**
			 * This function handles the display of the sub-menu page registered above
			 */
			function ere_register_taxonomy_page_display() {
			?>
				<div class="wrap">
			<?php
				if ( isset( $_REQUEST['view'] ) && 'edit' == $_REQUEST['view'] ) // User is requesting to edit a taxonomy
					include ERE_PLUGIN_DIR . '/includes/property-taxonomies/views/view-edit-property-taxonomy.php';
				else // Normal settings page view
					include ERE_PLUGIN_DIR . '/includes/property-taxonomies/views/view-property-taxonomies.php';
			?>
				</div>
			<?php
			}


		/**
		 * This function prints admin notices based on $_REQUEST parameters
		 */
		function admin_notices() {
			// Ignore this functionality if we're not on our menu page
			if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== self::$ere_property_menu_page )
				return;

			// Taxonomy creation
			if ( isset( $_REQUEST['created'] ) && 'true' == $_REQUEST['created'] )
				echo '<div id="message" class="updated"><p><strong>New taxonomy was successfully created!</strong></p></div>';
			// Taxonomy editing
			if ( isset( $_REQUEST['edited'] ) && 'true' == $_REQUEST['edited'] )
				echo '<div id="message" class="updated"><p><strong>Taxonomy was successfully edited!</strong></p></div>';
			// Taxonomy deletion
			if ( isset( $_REQUEST['deleted'] ) && 'true' == $_REQUEST['deleted'] )
				echo '<div id="message" class="updated"><p><strong>Taxonomy was successfully deleted!</strong></p></div>';
		}

		/**
		 * This function registers taxonomies.
		 */
		 function init() {
			// Register taxonomies
			self::register_taxonomies();

			// Register default terms
			if( ! $default_registered_terms = get_option( self::$ere_property_settings_field . '_default_registered_terms' ) )
				$default_registered_terms = array();

			if ( ! in_array( 'for-sale', $default_registered_terms ) ) { // For Sale
				wp_insert_term( 'For Sale', key( self::ere_property_default_taxonomy_details() ), array( 'description' => 'List properties for sale under this term.' ) );
				$default_registered_terms[] = 'for-sale';
				update_option( self::$ere_property_settings_field . '_default_registered_terms', $default_registered_terms );
			}
				
			if ( ! in_array( 'for-rent', $default_registered_terms ) ) { // For Rent
				wp_insert_term( 'For Rent', key( self::ere_property_default_taxonomy_details() ), array( 'description' => 'List properties for rent under this term.' ) );
				$default_registered_terms[] = 'for-rent';
				update_option( self::$ere_property_settings_field . '_default_registered_terms', $default_registered_terms );
			}
		}

		/**
		 * ------------------
		 * Internal Functions
		 * ------------------
		 */

		/**
		 * This function returns details for the "Type" taxonomy (default).
		 */
		function ere_property_default_taxonomy_details() {
			return array(
				'types' => array(
					'labels' => array(
						'name' => 'Types',
						'singular_name' => 'Type',
						'search_items' =>  'Search Types',
						'all_items' => 'All Types',
						'parent_item' => 'Parent Types',
						'parent_item_colon' => 'Parent Type:',
						'edit_item' => 'Edit Type', 
						'update_item' => 'Update Type',
						'add_new_item' => 'Add New Type',
						'new_item_name' => 'New Type',
						'menu_name' => 'Type',
						'popular_items' => 'Popular Types',
						'add_or_remove_items' => 'Add or Remove Types',
						'choose_from_most_used' => 'Choose from the most used Types'
					),
					'hierarchical' => true,
					'rewrite' => array( 'slug' => 'types' )
				)
			);
		}

		/**
		 * This function returns an array of existing taxonomies.
		 */
		function get_taxonomies() {
			// If the default taxonomy is still active
			if ( get_option( self::$ere_property_settings_field . '_use_default' ) )
				return array_merge( self::ere_property_default_taxonomy_details(), ( array ) get_option( self::$ere_property_settings_field ) );
			// Default taxonomy is not active
			else
				return ( array ) get_option( self::$ere_property_settings_field );
		}

		/**
		 * This function registers taxonomies.
		 */
		function register_taxonomies() {
			foreach( ( array ) self::get_taxonomies() as $id => $data )
				register_taxonomy( $id, array( 'ere_properties' ), $data );
		}

		/**
		 * This function creates a formatted taxonomy id (e.g. "My Taxonomy" = "my-taxonomy").
		 */
		function create_taxonomy_id( $name, $delimiter = '-' ) {
			$taxonomy_id = preg_replace( '/[^a-zA-Z0-9\/_|+ -]/', '', $name );

			return preg_replace( '/[\/_|+ -]+/', $delimiter, strtolower( trim( $taxonomy_id, '-' ) ) );
		}

		/**
		 * This function creates the necessary re-direct URL
		 */
		function redirect( $page, $query_args = array() ) {
			$url = html_entity_decode( menu_page_url( $page, false ) );

			foreach ( $query_args as $key => $value )
				if ( isset( $key ) && isset( $value ) )
					$url = add_query_arg( $key, $value, $url );

			wp_redirect( esc_url_raw( $url ) );
		}

		/**
		 * This function creates a taxonomy within our options.
		 * $args is POST
		 */
		function update_taxonomy( $args = array(), $edit = false ) {
			// Verify nonce is valid (checks for creation nonce or edit nonce)
			if ( ( isset( $_POST['ere_create_property_taxonomy_nonce'] ) && wp_verify_nonce( $_POST['ere_create_property_taxonomy_nonce'], 'ere_create_property_taxonomy' ) ) ||
				( $edit && isset( $_POST['ere_edit_property_taxonomy_nonce'] ) && wp_verify_nonce( $_POST['ere_edit_property_taxonomy_nonce'], 'ere_edit_property_taxonomy' ) ) ) {
				// Verify required fields
				if ( ! isset( $args['name'] ) || empty( $args['name'] ) || ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) )
					wp_die( 'Please fill out all of the required fields. <br /> <a href="' . admin_url( 'edit.php?post_type=' . self::$ere_property_post_type . '&page=' .self::$ere_property_menu_page ) . '">&laquo; Back</a>' );


				extract( $args );

				$taxonomy_id = ( isset( $id ) && ! empty( $id ) ) ? $id : self::create_taxonomy_id( $name );

				$args = array(
					'labels' => array(
						'name' => strip_tags( $name ),
						'singular_name' => strip_tags( $singular_name ),
						'search_items' => sprintf( 'Search %s', strip_tags( $name ) ),
						'all_items' => sprintf( 'All %s', strip_tags( $name ) ),
						'parent_item' => sprintf( 'Parent %s', strip_tags( $name ) ),
						'parent_item_colon' => sprintf( 'Parent %s:', strip_tags( $name ) ),
						'edit_item' => sprintf( 'Edit %s', strip_tags( $singular_name ) ),
						'update_item' => sprintf( 'Update %s', strip_tags( $singular_name ) ),
						'add_new_item' => sprintf( 'Add New %s', strip_tags( $singular_name ) ),
						'new_item_name' => sprintf( 'New %s Name', strip_tags( $singular_name ) ),
						'menu_name' => strip_tags( $name ),
						'popular_items' => sprintf( 'Popular %s', strip_tags( $name ) ),
						'add_or_remove_items' => sprintf( 'Add or Remove %s', strip_tags( $name ) ),
						'choose_from_most_used' => sprintf( 'Choose from the most used %s', strip_tags( $name ) )
					),
					'hierarchical' => true,
					'rewrite' => array( 'slug' => $taxonomy_id )
				);

				// Update the option (if it is not the default taxonomy)
				if ( ! array_key_exists( $taxonomy_id, self::ere_property_default_taxonomy_details() ) )
					update_option( self::$ere_property_settings_field, wp_parse_args( array( $taxonomy_id => $args ), get_option( self::$ere_property_settings_field ) ) );
				// Activate default taxonomy
				else
					update_option( self::$ere_property_settings_field . '_use_default', true );


				// If user is creating a new taxonomy
				if ( ! $edit )
					self::register_taxonomies(); // Register taxonomies

				flush_rewrite_rules(); // Flush rewrite rules to ensure permalinks work correctly

				self::redirect( self::$ere_property_menu_page, array( ( ! $edit ) ? 'created' : 'edited' => 'true' ) );
				exit;
			}
			else
				wp_die( 'Sorry, something went wrong. Please try again.' );
		}

		/**
		 * This function deletes a taxonomy from options.
		 */
		function delete_taxonomy( $id = false ) {
			// Verify nonce is valid
			if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'ere_delete_property_taxonomy' ) ) {
				// Verify required fields
				if ( ! isset( $id ) || empty( $id ) )
					wp_die( 'Sorry but that taxonomy doesn\'t exist. Please try again.' );

				$ere_property_option = get_option( self::$ere_property_settings_field );

				// Remove taxonomy if it exists
				if ( array_key_exists( $id, ( array ) $ere_property_option ) ) {
					unset( $ere_property_option[$id] );
					update_option( self::$ere_property_settings_field, $ere_property_option );
				}
				// Remove default taxonomy option
				else if ( array_key_exists( $id, self::ere_property_default_taxonomy_details() ) )
					update_option( self::$ere_property_settings_field . '_use_default', false );
				else
					wp_die( 'Sorry but that taxonomy doesn\'t exist. Please try again.' );


				self::redirect( self::$ere_property_menu_page, array( 'deleted' => 'true' ) );
				exit;
			}
			else
				wp_die( 'Sorry, something went wrong. Please try again.' );
		}
	}


	function Easy_Real_Estate_Property_Taxonomies_Instance() {
		return Easy_Real_Estate_Property_Taxonomies::instance();
	}

	Easy_Real_Estate_Property_Taxonomies_Instance();
}