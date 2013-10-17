<?php
/**
 * Easy_Real_Estate_Activation
 *
 * Description: Plugin activation
 *
 * @access      private
 * @since       1.0 
 * @return      void
 */

if( ! class_exists( 'Easy_Real_Estate_Activation' ) ) {
	class Easy_Real_Estate_Activation {
		private static $instance; // Keep track of the instance

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate_Activation;

			return self::$instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
		}

		/**
		 * This function fires on activation and flushes rewrite rules
		 */
		function activate() {
			Easy_Real_Estate_Post_Types::init(); // Registered post types must be added before flushing rewrite rules on activation
			flush_rewrite_rules();
		}
	}


	function Easy_Real_Estate_Activation_Instance() {
		return Easy_Real_Estate_Activation::instance();
	}

	Easy_Real_Estate_Activation_Instance();
}