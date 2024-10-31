<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select form section of feeds.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Cf7_Integration_With_Mautic
 * @subpackage Mwb_Cf7_Integration_With_Mautic/mwb-crm-fw/templates/meta-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$forms = isset( $params['forms'] ) ? $params['forms'] : array();

?>
<div class="mwb-feeds__content  mwb-content-wrap">
	<a class="mwb-feeds__header-link active">
		<?php esc_html_e( 'Select Form', 'mwb-cf7-integration-with-mautic' ); ?>
	</a>

	<div class="mwb-feeds__meta-box-main-wrapper">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select name="crm_form" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-cf7-select-form" class="mwb-form__dropdown">
					<option value="-1"><?php esc_html_e( 'Select Form', 'mwb-cf7-integration-with-mautic' ); ?></option>
					<optgroup label="<?php esc_html_e( 'Contact Form 7', 'mwb-cf7-integration-with-mautic' ); ?>" ></optgroup>
					<?php if ( ! empty( $forms ) && is_array( $forms ) ) : ?>
						<?php foreach ( $forms as $key => $value ) : ?>
							<option value="<?php echo esc_html( $value->ID ); ?>" <?php selected( $params['selected_form'], $value->ID ); ?>><?php echo esc_html( $value->post_title ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>
	</div>
</div>
