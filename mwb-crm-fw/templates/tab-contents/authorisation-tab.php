<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the accounts creation page.
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
$authentication_type = ! empty( $params['auth_type'] ) ? sanitize_text_field( wp_unslash( $params['auth_type'] ) ) : '';
$row_basic           = '';
$row_oauth2          = '';
if ( 'basic' === $authentication_type ) {
	$row_basic = 'is_hidden_accounts_tab';
} elseif ( 'oauth2' === $authentication_type ) {
	$row_oauth2 = 'is_hidden_accounts_tab';
} else {
	$row_basic  = 'is_hidden_accounts_tab';
	$row_oauth2 = 'is_hidden_accounts_tab';
}
?>
<?php if ( '1' !== get_option( 'mwb-' . $this->crm_slug . '-cf7-active', false ) ) : ?>
	<section class="mwb-intro">
		<div class="mwb-content-wrap">
			<div class="mwb-intro__header">
				<h2 class="mwb-section__heading">
					<?php
					echo sprintf(
						/* translators: %s: CRM name */
						esc_html__( 'Getting started with CF7 and %s', 'mwb-cf7-integration-with-mautic' ),
						esc_html( $this->crm_name )
					);
					?>
				</h2>
			</div>
			<div class="mwb-intro__body mwb-intro__content">
				<p>
				<?php
				echo sprintf(
					/* translators: %s: CRM name */
					esc_html__( 'With this CF7 %1$s Integration you can easily sync all your CF7 Form Submissions data over %2$s. It will create %3$s over %4$s, based on your CF7 Form Feed data.', 'mwb-cf7-integration-with-mautic' ),
					esc_html( $this->crm_name ),
					esc_html( $this->crm_name ),
					esc_html( $params['api_modules'] ),
					esc_html( $this->crm_name )
				);
				?>
				</p>
				<ul class="mwb-intro__list">
					<li class="mwb-intro__list-item">
						<?php
						echo sprintf(
							/* translators: %s: CRM name */
							esc_html__( 'Connect your %s account with CF7.', 'mwb-cf7-integration-with-mautic' ),
							esc_html( $this->crm_name )
						);
						?>
					</li>
					<li class="mwb-intro__list-item">
						<?php
						echo sprintf(
							/* translators: %s: CRM name */
							esc_html__( 'Sync your data over %s.', 'mwb-cf7-integration-with-mautic' ),
							esc_html( $this->crm_name )
						);
						?>
					</li>
				</ul>
				<div class="mwb-intro__button">
					<a href="javascript:void(0)" class="mwb-btn mwb-btn--filled" id="mwb-showauth-form">
						<?php esc_html_e( 'Connect your Account.', 'mwb-cf7-integration-with-mautic' ); ?>
					</a>
				</div>
			</div> 
		</div>
	</section>

	<!--============================================================================================
											Authorization form start.
	================================================================================================-->

	<div class="mwb_sf_cf7__account-wrap row-hide" id="mwb-cf7-auth_wrap">
		<!-- Logo section start -->
		<div class="mwb-sf_cf7__logo-wrap">
			<div class="mwb-sf_cf7__logo-mautic">
				<img src="<?php echo esc_url( MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/mautic-logo.png' ); ?>" alt="<?php esc_html_e( 'Mautic', 'mwb-cf7-integration-with-mautic' ); ?>">
			</div>
			<div class="mwb-sf_cf7__logo-contact">
				<img src="<?php echo esc_url( MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/contact-form.svg' ); ?>" alt="<?php esc_html_e( 'CF7', 'mwb-cf7-integration-with-mautic' ); ?>">
			</div>
		</div>
		<!-- Logo section end -->

		<!-- Login form start -->
		<form method="post" id="mwb_sf_cf7_account_form">

			<div class="mwb_sf_cf7_table_wrapper">
				<div class="mwb_sf_cf7_account_setup">
					<h2>
						<?php esc_html_e( 'Enter your credentials here', 'mwb-cf7-integration-with-mautic' ); ?>
					</h2>
				</div>

				<table class="mwb_sf_cf7_table">
					<tbody>
					<?php
					if ( isset( $params['connection'] ) && !empty($params['connection']['success']) ) { // phpcs:ignore
						if ( ! empty( $params['connection']['class'] ) ) {
							$params['admin_class']::mwb_sf_cf7_notices( $params['connection']['class'], $params['connection']['msg'] );
							delete_option( 'mwb-' . $this->crm_slug . '-cf7-connection-data' );
						}
					}
					?>

						<!-- Connection Type start  -->
						<tr>
							<th>							
								<label><?php esc_html_e( 'Authentication Type', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$a_type = ! empty( $params['auth_type'] ) ? sanitize_text_field( wp_unslash( $params['auth_type'] ) ) : '';
								?>

								<select  name="mwb_account[a_type]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-auth_type">
									<option value="-1" selected><?php esc_html_e( '--Select--', 'mwb-cf7-integration-with-mautic' ); ?></option>
									<option value="basic" <?php selected( $a_type, 'basic' ); ?> ><?php esc_html_e( 'Basic', 'mwb-cf7-integration-with-mautic' ); ?></option>
									<option value="oauth2" <?php selected( $a_type, 'oauth2' ); ?> ><?php esc_html_e( 'OAuth2', 'mwb-cf7-integration-with-mautic' ); ?></option>
								</select>

							</td>
						</tr>
						<!-- Connection Type end -->

						<!-- Client ID start  -->
						<tr id="row-hide-clientid" class="<?php echo esc_html( $row_basic ); // phpcs:ignore ?>">
							<th>							
								<label><?php esc_html_e( 'Client ID', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$consumer_key = ! empty( $params['consumer_key'] ) ? sanitize_text_field( wp_unslash( $params['consumer_key'] ) ) : '';
								?>
								<div class="mwb-sf-cf7__secure-field">
									<input type="password"  name="mwb_account[app_id]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-consumer-key" value="<?php echo esc_html( $consumer_key ); ?>">
									<div class="mwb-sf-cf7__trailing-icon">
										<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
									</div>
								</div>
							</td>
						</tr>
						<!-- Client ID end -->

						<!-- User Name start  -->
						<tr id="row-hide-username" class="<?php echo esc_html( $row_oauth2 ); // phpcs:ignore ?>">
							<th>							
								<label><?php esc_html_e( 'User Name', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$username = ! empty( $params['username'] ) ? sanitize_text_field( wp_unslash( $params['username'] ) ) : '';
								?>
								<div class="mwb-sf-cf7__secure-field">
									<input type="text"  name="mwb_account[username]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-username" value="<?php echo esc_html( $username ); ?>">
								</div>
							</td>
						</tr>
						<!-- User Name end -->

						<!-- Password start  -->
						<tr id="row-hide-password" class="<?php echo esc_html( $row_oauth2 ); // phpcs:ignore ?>">
							<th>							
								<label><?php esc_html_e( 'Password', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$password = ! empty( $params['password'] ) ? sanitize_text_field( wp_unslash( $params['password'] ) ) : '';
								?>
								<div class="mwb-sf-cf7__secure-field">
									<input type="password"  name="mwb_account[password]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-password" value="<?php echo esc_html( $password ); ?>">
									<div class="mwb-sf-cf7__trailing-icon">
										<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
									</div>
								</div>
							</td>
						</tr>
						<!-- Password end -->

						<!-- Client Secret ID start -->
						<tr id="row-hide-secretid" class="<?php echo esc_html( $row_basic ); // phpcs:ignore ?>">
							<th>
								<label><?php esc_html_e( 'Client Secret ID', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$secret_key = ! empty( $params['consumer_secret'] ) ? sanitize_text_field( wp_unslash( $params['consumer_secret'] ) ) : '';
								?>

								<div class="mwb-sf-cf7__secure-field">
									<input type="password" name="mwb_account[secret_key]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-consumer-secret" value="<?php echo esc_html( $secret_key ); ?>">
									<div class="mwb-sf-cf7__trailing-icon">
										<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
									</div>
								</div>
							</td>
						</tr>
						<!-- Client Secret ID End -->

						<!-- Mautic url start -->
						<tr class="">
							<th>
								<label><?php esc_html_e( 'Mautic URL', 'mwb-cf7-integration-with-mautic' ); ?></label>
							</th>

							<td>
								<?php
								$mautic_url = ! empty( $params['redirect_url'] ) ? sanitize_text_field( wp_unslash( $params['redirect_url'] ) ) : '';
								?>
								<input type="url" name="mwb_account[redirect_url]" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-redirect-url" value="<?php echo esc_html( $mautic_url ); ?>">
							</td>
						</tr>
						<!-- Mautic url end -->

						<!-- Save & connect account start -->
						<tr>
							<th>
							</th>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?mwb-cf7-perform-auth=1' ) ) ); ?>" class="mwb-btn mwb-btn--filled mwb_sf_cf7_submit_account" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>_cf7-authorize-button" ><?php esc_html_e( 'Authorize', 'mwb-cf7-integration-with-mautic' ); ?></a>
							</td>
						</tr>
						<!-- Save & connect account end -->
					</tbody>
				</table>
			</div>
		</form>
		<!-- Login form end -->

		<!-- Info section start -->
		<div class="mwb-intro__bottom-text-wrap ">
			<p>
				<?php esc_html_e( 'Don’t have a Mautic account yet . ', 'mwb-cf7-integration-with-mautic' ); ?>
				<a href="https://www.mautic.org/download" target="_blank" class="mwb-btn__bottom-text">
					<?php esc_html_e( 'Click here to download', 'mwb-cf7-integration-with-mautic' ); ?>
				</a>
			</p>
			<p>
				<?php esc_html_e( 'After downloading mautic .', 'mwb-cf7-integration-with-mautic' ); ?>
				<a href="https://www.mautic.org/getting-started" target="_blank" class="mwb-btn__bottom-text"><?php esc_html_e( 'see instructions to setup mautic on your server', 'mwb-cf7-integration-with-mautic' ); ?></a>
			</p>
			<p>
				<?php esc_html_e( 'Check app setup guide . ', 'mwb-cf7-integration-with-mautic' ); ?>
				<a href="javascript:void(0)" class="mwb-btn__bottom-text trigger-setup-guide"><?php esc_html_e( 'Show Me How', 'mwb-cf7-integration-with-mautic' ); ?></a>
			</p>
		</div>
		<!-- Info section end -->
	</div>

<?php else : ?>

	<!-- Successfull connection start -->
	<section class="mwb-sync">
		<div class="mwb-content-wrap">
			<div class="mwb-sync__header">
				<h2 class="mwb-section__heading">
					<?php
					echo sprintf(
						/* translators: %s: CRM name */
						esc_html__( 'Congrats! You’ve successfully set up the MWB CF7 Integration with %s Plugin.', 'mwb-cf7-integration-with-mautic' ),
						esc_html( $this->crm_name )
					);
					?>
				</h2>
			</div>
			<div class="mwb-sync__body mwb-sync__content-wrap">            
				<div class="mwb-sync__image">    
					<img src="<?php echo esc_url( MWB_CF7_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/congo.jpg' ); ?>" >
				</div>       
				<div class="mwb-sync__content">            
					<p> 
						<?php
						echo sprintf(
							/* translators: %s: CRM name */
							esc_html__( 'Now you can go to the dashboard and check connection data. You can create your feeds, edit them in the feeds tab. If you do not see your data over %s, you can check the logs for any possible error.', 'mwb-cf7-integration-with-mautic' ),
							esc_html( $this->crm_name )
						);
						?>
					</p>
					<div class="mwb-sync__button">
						<a href="javascript:void(0)" class="mwb-btn mwb-btn--filled mwb-onboarding-complete">
							<?php esc_html_e( 'View Dashboard', 'mwb-cf7-integration-with-mautic' ); ?>
						</a>
					</div>
				</div>             
			</div>       
		</div>
	</section>
	<!-- Successfull connection end -->

<?php endif; ?>
