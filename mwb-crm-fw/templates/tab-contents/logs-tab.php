<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the mautic logs listing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Cf7_Integration_With_Mautic
 * @subpackage Mwb_Cf7_Integration_With_Mautic/mwb-crm-fw/templates/tab-contents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$default_setting      = get_option( 'mwb-' . $this->crm_slug . '-cf7-setting', $this->connect_manager->get_plugin_default_settings() );
$params['log_enable'] = false;
if ( ! empty( $default_setting['enable_logs'] ) ) {
	$params['log_enable'] = true;
}
?>
<div class="mwb-sf_cf7__logs-wrap" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-cf7-logs" ajax_url="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>">
	<div class="mwb-sf_cf7__logo-wrap">
		<div class="mwb-sf_cf7__logo-mautic">
			<img src="<?php echo esc_url( MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/mautic-logo.png' ); ?>" alt="<?php esc_html_e( 'Mautic', 'mwb-cf7-integration-with-mautic' ); ?>">
		</div>
		<div class="mwb-sf_cf7__logo-contact">
			<img src="<?php echo esc_url( MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/contact-form.svg' ); ?>" alt="<?php esc_html_e( 'CF7', 'mwb-cf7-integration-with-mautic' ); ?>">
		</div>
		<?php if ( $params['log_enable'] ) : ?>
				<ul class="mwb-logs__settings-list">
					<li class="mwb-logs__settings-list-item">
						<a id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-cf7-clear-log" href="javascript:void(0)" class="mwb-logs__setting-link">
							<?php esc_html_e( 'Clear Log', 'mwb-cf7-integration-with-mautic' ); ?>	
						</a>
					</li>
					<li class="mwb-logs__settings-list-item">
						<a id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-cf7-download-log" href="javascript:void(0)"  class="mwb-logs__setting-link">
							<?php esc_html_e( 'Download', 'mwb-cf7-integration-with-mautic' ); ?>	
						</a>
					</li>
				</ul>
		<?php endif; ?>
	</div>
	<div>
		<div>
			<?php if ( $params['log_enable'] ) : ?>
			<div class="mwb-sf_cf7__logs-table-wrap">
				<table id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-cf7-table" class="display mwb-sf__logs-table dt-responsive nowrap" width="100%">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Expand', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Feed', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Feed ID', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Mautic Object', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Mautic ID', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Event', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th><?php esc_html_e( 'Timestamp', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th class=""><?php esc_html_e( 'Request', 'mwb-cf7-integration-with-mautic' ); ?></th>
							<th class=""><?php esc_html_e( 'Response', 'mwb-cf7-integration-with-mautic' ); ?></th>
						</tr>
					</thead>
				</table>
			</div>
			<?php else : ?>
				<div class="mwb-content-wrap">
					<?php esc_html_e( 'Please enable the logs from ', 'mwb-cf7-integration-with-mautic' ); ?><a href="<?php echo esc_url( 'admin.php?page=mwb_' . $params['crm_slug'] . '_cf7_page&tab=settings' ); ?>" target="_blank"><?php esc_html_e( 'Settings tab', 'mwb-cf7-integration-with-mautic' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
