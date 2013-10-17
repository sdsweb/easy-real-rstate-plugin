<?php
/**
 * Easy_Real_Estate_Deactivation
 *
 * Description: Plugin deactivation
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Deactivation' ) ) {
	class Easy_Real_Estate_Deactivation {

		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Deactivation;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
		}

		/**
		 * This function fires on deactivation and flushes rewrite rules
		 */
		function deactivate() {
			global $wp_post_types;
			
			Easy_Real_Estate_Deactivation::unregister_post_type( 'ere_agents' ); // Unregister Agents Post Type
			Easy_Real_Estate_Deactivation::unregister_post_type( 'ere_properties' ); // Unregister Properties Post Type
			Easy_Real_Estate_Deactivation::unregister_post_type( 'ere_testimonials' ); // Unregister Testimonials Post Type

			flush_rewrite_rules();
		}

		/*
		 * This function is a custom function to unregister post types
		 */
		function unregister_post_type( $post_type ) {
			global $wp_post_types;

			if ( isset( $wp_post_types[ $post_type ] ) ) {
				unset( $wp_post_types[ $post_type ] );
				return true;
			}

			return false;
		}
	}


	function Easy_Real_Estate_Deactivation_Instance() {
		return Easy_Real_Estate_Deactivation::instance();
	}

	Easy_Real_Estate_Deactivation_Instance();
}