<?php
/**
 * Easy_Real_Estate_Widgets
 *
 * Description: Initalize Widgets
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Widgets' ) ) {
	class Easy_Real_Estate_Widgets {

		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Widgets;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
			add_action( 'widgets_init', array( $this, 'widgets_init' ) ); // Register and Initalize Widgets
		}

		/**
		 * This function registers and initalizes all widgets
		 */
		function widgets_init() {
			// Random Testimonials Widget
			include_once ERE_PLUGIN_DIR . 'includes/widgets/testimonials/Random_Testimonials_Widget.php';
			register_widget( 'Random_Testimonials_Widget' );

			// Featured Content Widget
			include_once ERE_PLUGIN_DIR . 'includes/widgets/featured-content/Featured_Content_Widget.php';
			register_widget( 'Featured_Content_Widget' );

			// Search Widget
			include_once ERE_PLUGIN_DIR . 'includes/widgets/search/Search_Widget.php';
			register_widget( 'Search_Widget' );

			// Agents Widget
			include_once ERE_PLUGIN_DIR . 'includes/widgets/agents/Agents_Widget.php';
			register_widget( 'Agents_Widget' );
		}
	}


	function Easy_Real_Estate_Widgets_Instance() {
		return Easy_Real_Estate_Widgets::instance();
	}

	Easy_Real_Estate_Widgets_Instance();
}