<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com
 * @since             1.0.0
 * @package           Mwb_Cf7_Integration_With_Mautic
 *
 * @wordpress-plugin
 * Plugin Name:       MWB CF7 Integration With Mautic
 * Plugin URI:        https://wordpress.org/plugins/mwb-cf7-integration-with-mautic/
 * Description:       This plugin integrates your Mautic account with CF7, sending all data over Mautic as per its available modules.
 * Version:           1.0.1
 * Requires at least: 4.4
 * Tested up to:      5.8.2
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       mwb-cf7-integration-with-mautic
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check for plugin dependency
 *
 * @since     1.0.0
 * @return    array
 */
function mwb_mtc_cf7_plugin_activation() {
	$active['status'] = false;
	$active['msg']    = 'cf7_inactive';

	if ( true === mwb_mtc_cf7_is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$active['status'] = true;
		$active['msg']    = '';
	}

	return $active;
}

/**
 * Function to check for plugin activation.
 *
 * @param    string $slug   Slug of the plugin.
 * @return   bool
 */
function mwb_mtc_cf7_is_plugin_active( $slug = '' ) {

	if ( empty( $slug ) ) {
		return;
	}

	$active_plugins = get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $slug, $active_plugins, true ) || array_key_exists( $slug, $active_plugins );
}

$mtc_cf7_plugin_activation = mwb_mtc_cf7_plugin_activation();

if ( true == $mtc_cf7_plugin_activation['status'] ) { // phpcs:ignore
	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'MWB_CF7_INTEGRATION_WITH_MAUTIC_VERSION', '1.0.0' );

	// Define all the necessary details of the plugin.
	define( 'MWB_CF7_INTEGRATION_WITH_MAUTIC_URL', plugin_dir_url( __FILE__ ) ); // Plugin URL directory path.
	define( 'MWB_CF7_INTEGRATION_WITH_MAUTIC_DIRPATH', plugin_dir_path( __FILE__ ) ); // Plugin filesystem directory path.
	define( 'MWB_CF7_MAUTIC_ONBOARD_PLUGIN_NAME', 'MWB CF7 Integration with Mautic' );
	/**
	 * Returns current CRM name & slug.
	 *
	 * @param    string $get     Value to retrieve i.e slug or name.
	 * @since    1.0.0
	 * @return   string
	 */
	function get_current_crm_mautic( $get = '' ) {
		$slug = 'mautic';
		if ( 'slug' == $get ) { // phpcs:ignore
			return esc_html( ( $slug ) );
		} else {
			return esc_html( ucwords( $slug ) );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-mwb-cf7-integration-with-mautic-activator.php
	 */
	function activate_mwb_cf7_integration_with_mautic() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-cf7-integration-with-mautic-activator.php';
		Mwb_Cf7_Integration_With_Mautic_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-mwb-cf7-integration-with-mautic-deactivator.php
	 */
	function deactivate_mwb_cf7_integration_with_mautic() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-cf7-integration-with-mautic-deactivator.php';
		Mwb_Cf7_Integration_With_Mautic_Deactivator::deactivate();
	}

	if ( ! class_exists( 'Cf7_Integration_With_Mautic_Admin' ) ) {
		// Add settings link in plugin action links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_mtc_cf7_settings_link' );

		/**
		 * Add settings link callback.
		 *
		 * @since  1.0.0
		 * @param  string $links link to the admin area of the plugin.
		 * @return array
		 */
		function mwb_mtc_cf7_settings_link( $links ) {

			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=mwb_' . get_current_crm_mautic( 'slug' ) . '_cf7_page&tab=accounts' ) . '">' . esc_html__( 'Settings', 'mwb-cf7-integration-with-mautic' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}
	}

	/**
	 * Pro dependency check.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	function pro_dependency_check_new() {
		// Check if pro plugin exists.
		if ( mwb_mtc_cf7_is_plugin_active( 'cf7-integration-with-mautic/cf7-integration-with-mautic.php' ) ) {

			if ( class_exists( 'Cf7_Integration_With_Mautic_Admin' ) ) {
				return true;
			}
		}

		return false;
	}

	add_filter( 'plugin_row_meta', 'mwb_mtc_cf7_important_links', 10, 2 );

	/**
	 * Add custom links.
	 *
	 * @param   string $links   Link to index file of plugin.
	 * @param   string $file    Index file of plugin.
	 * @since   1.0.0
	 * @return  array
	 */
	function mwb_mtc_cf7_important_links( $links, $file ) {

		if ( strpos( $file, 'mwb-cf7-integration-with-mautic.php' ) !== false ) {

			$row_meta = array(
				'demo'    => '<a href="' . esc_url( 'https://demo.makewebbetter.com/get-personal-demo/mwb-cf7-integration-with-mautic/?utm_source=MWB-cf7mautic-org&utm_medium=MWB-org-backend&utm_campaign=demo' ) . '" target="_blank"><img src="' . MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/Demo.svg" style="width: 20px;padding-right: 5px;"></i>' . esc_html__( 'Demo', 'mwb-cf7-integration-with-mautic' ) . '</a>',
				'doc'     => '<a href="' . esc_url( 'https://docs.makewebbetter.com/mwb-cf7-integration-with-mautic/?utm_source=MWB-cf7mautic-org&utm_medium=MWB-org-backend&utm_campaign=doc' ) . '" target="_blank"><img src="' . MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/Documentation.svg" style="width: 20px;padding-right: 5px;"></i>' . esc_html__( 'Documentation', 'mwb-cf7-integration-with-mautic' ) . '</a>',
				'support' => '<a href="' . esc_url( 'https://support.makewebbetter.com/wordpress-plugins-knowledge-base/category/mwb-cf7-integration-with-mautic/?utm_source=MWB-cf7mautic-org&utm_medium=MWB-org-backend&utm_campaign=support' ) . '" target="_blank"><img src="' . MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/Support.svg" style="width: 20px;padding-right: 5px;"></i>' . esc_html__( 'Support', 'mwb-cf7-integration-with-mautic' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	register_activation_hook( __FILE__, 'activate_mwb_cf7_integration_with_mautic' );
	register_deactivation_hook( __FILE__, 'deactivate_mwb_cf7_integration_with_mautic' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-mwb-cf7-integration-with-mautic.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_mwb_cf7_integration_with_mautic() {

		$plugin = new Mwb_Cf7_Integration_With_Mautic();
		$plugin->run();

	}
	run_mwb_cf7_integration_with_mautic();
} else {

	// Deactivate the plugin if Contact form 7 is not active.
	add_action( 'admin_init', 'mwb_mtc_cf7_activation_failure' );

	/**
	 * Deactivate the plugin.
	 */
	function mwb_mtc_cf7_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_mtc_cf7_activation_notice' );

	/**
	 * This function displays plugin activation error notices.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function mwb_mtc_cf7_activation_notice() {

		global $mtc_cf7_plugin_activation;

		// To hide Plugin activated notice.
		unset( $_GET['activate'] ); // phpcs:ignore

		?>

		<?php if ( 'cf7_inactive' === $mtc_cf7_plugin_activation['msg'] ) { ?>

			<div class="notice notice-error is-dismissible">
				<p>
					<strong>
						<?php esc_html_e( 'Contatc Form 7', 'mwb-cf7-integration-with-mautic' ); ?>
					</strong>
					<?php esc_html_e( ' is not activated, Please activate Contact Form 7 first to activate ', 'mwb-cf7-integration-with-mautic' ); ?>
					<strong>
						<?php esc_html_e( ' MWB CF7 Integration with Mautic', 'mwb-cf7-integration-with-mautic' ); ?>
					</strong>
				</p>
			</div>

			<?php
		}
	}
}
