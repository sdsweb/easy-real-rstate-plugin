<?php
/**
 * Easy_Real_Estate_Sidebars
 *
 * Description: Registers sidebars for specific widget areas.
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Sidebars' ) ) {
	class Easy_Real_Estate_Sidebars {

		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Sidebars;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
			add_action( 'widgets_init', array( $this, 'widgets_init' ) ); // Register Sidebars
			add_action( 'tha_header_after', array( $this, 'tha_header_after' ) ); // Output Property Search Sidebar
		}

		/**
		 * This function registers all custom post types (with post meta/taxonomies, etc...)
		 */
		function widgets_init() {
			// Property Search Sidebar
			register_sidebar( array(
				'name' => 'Property Search Sidebar',
				'id' => 'ere-property-search-sidebar',
				'description' => 'This sidebar is meant to be displayed across all pages on the theme below the header. *Only place Property Search Widget here*',
				'class' => 'ere-property-search-sidebar property-search-sidebar',
				'before_widget' => '<section id="ere-property-search-widget-%1$s" class="widget property-search-widget ere-property-search-widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h2 class="widgettitle widget-title ere-property-search-title property-search-title">',
				'after_title' => '</h2>'
			) );
		}

		/**
		 * This function outputs the Property Search Sidebar.
		 */
		function tha_header_after() {
			if ( is_active_sidebar( 'ere-property-search-sidebar' ) ) : // Display Property Search Sidebar
			?>
				<div class="in">
					<section id="property-search-sidebar" class="property-search-sidebar">
						<?php dynamic_sidebar( 'ere-property-search-sidebar' ); ?>
					</section>
				</div>
			<?php
			endif;
		}
	}


	function Easy_Real_Estate_Sidebars_Instance() {
		return Easy_Real_Estate_Sidebars::instance();
	}

	Easy_Real_Estate_Sidebars_Instance();
}