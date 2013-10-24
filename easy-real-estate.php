<?php
/**
 * Plugin Name: Easy Real Estate
 * Plugin URI: https://github.com/sdsweb/Easy-Real-Estate-Plugin
 * Description: A plugin from Slocum Design Studio to add Real Estate functionality to our Modern Real Estate theme.
 * Author: Slocum Design Studio
 * Author URI: http://www.slocumstudio.com
 * Version: 1.0.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl.html

 * Easy Real Estate WordPress plugin, Copyright (C) 2013 Slocum Studio
 * Easy Real Estate WordPress plugin is licensed under the GPL.
 */

define( 'ERE_VERSION', '1.0.1' ); // Version
define( 'ERE_PLUGIN_FILE', __FILE__ ); // Reference to this plugin file
define( 'ERE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); // Plugin directory path
define( 'ERE_PLUGIN_URL', trailingslashit( plugins_url( '' , __FILE__ ) ) ); // Plugin url

if( ! class_exists( 'Easy_Real_Estate' ) ) {
	class Easy_Real_Estate {
		private static $instance; // Keep track of the instance

		/*
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new Easy_Real_Estate;

			return self::$instance;
		}


		/*
		 * This function sets up all of the actions and filters on instance
		 */
		function __construct( ) {
			 // Plugin Activation
			include_once ERE_PLUGIN_DIR . 'includes/activation.php';
			register_activation_hook( ERE_PLUGIN_FILE, array( 'Easy_Real_Estate_Activation', 'activate' ) );

			 // Plugin De-Activation
			include_once ERE_PLUGIN_DIR . 'includes/deactivation.php';
			register_deactivation_hook( ERE_PLUGIN_FILE, array( 'Easy_Real_Estate_Deactivation', 'deactivate' ) );

			 // Plugin Updates
			include_once ERE_PLUGIN_DIR . 'includes/plugin-update-checker.php';
			$ere_updates = new PluginUpdateChecker(
				'http://theme-api.slocumstudio.com/easy-real-estate/info.php',
				__FILE__,
				'easy-real-estate'
			);
			add_filter( 'puc_request_info_query_args-easy-real-estate', array( $this, 'ere_update_query_args' ) );
			add_filter( 'puc_request_info_result-easy-real-estate', array( $this, 'ere_request_info_result' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Remove update notices if the versions are synced
			add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) ); // Create dashboard notification for updates
			add_action( 'wp_ajax_dismiss_ere_update_notification', array( $this, 'wp_ajax_dismiss_ere_update_notification' ) ); // Handle AJAX request for dismissing notifications


			// Register Post Types - Testimonials, Agents, and Properties
			include_once ERE_PLUGIN_DIR . 'includes/post-types/post-types.php';

			// Property Taxonomies - Allows creation, editing, and deletion of taxonomies and terms for the Properties post type, creates setting page under Properties post type
			include_once ERE_PLUGIN_DIR . 'includes/property-taxonomies/property-taxonomies.php';

			// Sidebar Init - Initalize sidebars
			include_once ERE_PLUGIN_DIR . 'includes/sidebars.php';

			// Widgets Init - Initalize widgets
			include_once ERE_PLUGIN_DIR . 'includes/widgets/widgets.php';
		}

		function ere_update_query_args( $args ) {
			$args['tt'] = time();
			$args['uid'] = md5( uniqid( rand(), true ) );
			return $args;
		}

		function ere_request_info_result( $plugin_info, $result ) {
			// Update is available (store option)
			if( version_compare( ERE_VERSION, $plugin_info->version, '<' ) )
				update_option( 'ere_update_available', $plugin_info->version );

			return $plugin_info;
		}

		/*
		 * This function creates a dashboard widget which displays an update notification if updates are available.
		 */
		function wp_dashboard_setup() {
			// Only display the message to administrators
			if ( current_user_can( 'update_plugins' ) ) {
				$ere_update_available = get_option( 'ere_update_available' );
				$ere_update_message_dismissed = get_option( 'ere_update_message_dismissed' );

				// If the user has not already dismissed the message for this version
				if ( version_compare( $ere_update_message_dismissed, $ere_update_available, '<' ) ) {
		?>
					<div class="updated" style="padding: 15px; position: relative;" id="ere_dashboard_message" data-version="<?php echo $ere_update_available; ?>">
						<strong>There is a new update for Easy Real Estate (v<?php echo $ere_update_available; ?>). You're currently using version <?php echo ERE_VERSION; ?>. <a href="plugins.php">Download Update</a>.</strong>
						<a href="javascript:void(0);" onclick="ereDismissUpgradeMessage();" style="float: right;">Dismiss.</a>
					</div>
					<script type="text/javascript">
						<?php  $ajax_nonce = wp_create_nonce( 'dismiss_ere_update_notification' ); ?>
						function ereDismissUpgradeMessage() {
							var ere_data = {
								action: 'dismiss_ere_update_notification',
								_wpnonce: '<?php echo $ajax_nonce; ?>',
								version: jQuery( '#ere_dashboard_message' ).attr( 'data-version' )
							};

							jQuery.post( ajaxurl, ere_data, function( response ) {
								jQuery( '#ere_dashboard_message').fadeOut();
							} );
						}
					</script>
		<?php
				}
			}
		}

		function wp_ajax_dismiss_ere_update_notification() {
			check_ajax_referer( 'dismiss_ere_update_notification' );

			if ( isset( $_POST['version'] ) && ! empty( $_POST['version'] ) ) {
				update_option( 'ere_update_message_dismissed', $_POST['version'] );
				echo 'true';
			}
			else
				echo 'false';
			exit;
		}

		function admin_init() {
			$ere_update_available = get_option( 'ere_update_available' );
			if ( version_compare( ERE_VERSION, $ere_update_available, '=' ) )
				update_option( 'ere_update_message_dismissed', ERE_VERSION );
		}
	}


	function Easy_Real_Estate_Instance() {
		return Easy_Real_Estate::instance();
	}

	Easy_Real_Estate_Instance();
}