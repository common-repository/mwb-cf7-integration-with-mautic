<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Cf7_Integration_With_Mautic
 * @subpackage Mwb_Cf7_Integration_With_Mautic/mwb-crm-fw/framework
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mwb_Cf7_Integration_With_Mautic
 * @subpackage Mwb_Cf7_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Mwb_Cf7_Integration_Mautic_Request_Module {

	/**
	 *  The instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $_instance    The instance of this class.
	 */
	private static $_instance; // phpcs:ignore

	/**
	 * Current crm slug.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $crm_slug    The current crm slug.
	 */
	public $crm_slug;

	/**
	 * Current crm name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string   $crm_name    The current crm name.
	 */
	public $crm_name;

	/**
	 * Instance of the Mwb_Cf7_Integration_Mautic_Api_Base class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object   $crm_api_module   Instance of Mwb_Cf7_Integration_Mautic_Api_Base class.
	 */
	public $crm_api_module;

	/**
	 *  The instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $feed id to record  The feed to sync.
	 */
	public static $feed_id;

	/**
	 * Main Mwb_Cf7_Integration_Mautic_Request_Module Instance.
	 *
	 * Ensures only one instance of Mwb_Cf7_Integration_Mautic_Request_Module is loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Mwb_Cf7_Integration_Mautic_Request_Module - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Initialise CRM name and slug.
		$this->crm_slug = get_current_crm_mautic( 'slug' );
		$this->crm_name = get_current_crm_mautic();

		// Initialise CRM API class.
		$crm_class            = 'Mwb_Cf7_Integration_' . $this->crm_name . '_Api_Base';
		$this->crm_api_module = $crm_class::get_instance();
	}


	/**
	 * Returns the requested values for required form index.
	 *
	 * @param     string $key      The form field meta key.
	 * @param     array  $data     An array of form entries data.
	 * @since     1.0.0
	 * @return    string           The post meta values.
	 */
	public function get_prop_value( $key = false, $data = array() ) {

		if ( empty( $key ) || ! is_array( $data ) ) {
			return;
		}

		foreach ( $data as $field => $value ) {
			if ( $key == $field ) { // @codingStandardsIgnoreLine
				return $value;
			}
		}
	}

	/**
	 * Returns the feed data we require.
	 *
	 * @param     int    $feed_id      The object id for post type feed.
	 * @param     string $meta_key     The object meta key for post type feed.
	 * @return    array|bool The current data for required object.
	 * @since 1.0.0
	 */
	public function get_feed( $feed_id = false, $meta_key = 'mapping_data' ) {
		if ( false == $feed_id ) { // phpcs:ignore
			return;
		}

		if ( 'mapping_data' == $meta_key ) { // phpcs:ignore
			$meta_key = 'mwb-' . $this->crm_slug . '-cf7-mapping-data';
		}

		$mapping = get_post_meta( $feed_id, $meta_key, true );

		if ( empty( $mapping ) ) {
			$mapping = false;
		}

		return $mapping;
	}

	/**
	 * Returns the mapping step we require.
	 *
	 * @param string $crm_obj  The CRM module.
	 * @param string $feed_id  Feed ID.
	 * @param string $entries  Current form entries.
	 *
	 * @since  1.0.0
	 * @return array The current mapping step required.
	 */
	public function get_crm_request( $crm_obj = false, $feed_id = false, $entries = array() ) {
		if ( false == $crm_obj || false == $feed_id || ! is_array( $entries ) ) { // phpcs:ignore
			return;
		}

		$feed = $this->get_feed( $feed_id ); // Get feed mapping data.

		if ( empty( $feed ) ) {
			return false;
		}

		// Process Feeds.
		$response = array();

		foreach ( $feed as $k => $mapping ) {

			$field_type = ! empty( $mapping['field_type'] ) ? $mapping['field_type'] : 'standard_field';

			switch ( $field_type ) {

				case 'standard_field':
					$field_format = ! empty( $mapping['field_value'] ) ? $mapping['field_value'] : '';
					$meta_key     = substr( $field_format, 11 );
					$field_value  = $this->get_prop_value( $meta_key, $entries );
					break;

				case 'custom_value':
					$field_key = ! empty( $mapping['custom_value'] ) ? $mapping['custom_value'] : '';

					preg_match_all( '/{(.*?)}/', $field_key, $dynamic_strings );

					if ( ! empty( $dynamic_strings[1] ) ) {
						$dynamic_values = $dynamic_strings[1];

						foreach ( $dynamic_values as $key => $value ) {
							$field_format = substr( $value, 11 );
							$field_value  = $this->get_prop_value( $field_format, $entries );

							$substr = '{' . $value . '}';

							$field_key   = str_replace( $substr, $field_value, $field_key );
							$field_value = $field_key;
						}
					}
					break;
			}
			if ( isset( $field_value[0] ) ) {
				if ( ! file_exists( $field_value[0] ) ) {
					$response[ $k ] = ! empty( $field_value ) ? $field_value : '';
				}
			}
		}

		$response = apply_filters( 'mwb_' . $this->crm_slug . '_cf7_request_data', $response, $crm_obj, $feed_id, $entries );
		return $response;
	}

	/**
	 * Replace the occurence within the string only once.
	 *
	 * @param string $from    The sub-string before replace.
	 * @param string $to      The sub-string after replace.
	 * @param string $content The string from which we operate.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function str_replace_first( $from, $to, $content ) {
		$from = '/' . preg_quote( $from, '/' ) . '/';

		return preg_replace( $from, $to, $content, 1 );
	}

	/**
	 * Retrive form data
	 *
	 * @param     object $form   Current submitted Form Data.
	 * @since     1.0.0
	 * @return    array
	 */
	public function retrieve_form_data( $form ) {

		$form_tags    = array();
		$form_id      = $form->id();
		$cf7_submit   = WPCF7_Submission::get_instance();
		$form_entries = $cf7_submit->uploaded_files();

		if ( ! is_array( $form_entries ) ) {
			$form_entries = array();
		}

		$form_title = $form->title();
		$form_input = get_post_meta( $form_id, '_form', true );

		if ( class_exists( 'WPCF7_FormTagsManager' ) ) {
			$tag_manager = WPCF7_FormTagsManager::get_instance();
			$tag_manager->scan( $form_input );
			$form_tags = $tag_manager->get_scanned_tags();

		} elseif ( class_exists( 'WPCF7_ShortcodeManager' ) ) {
			$tag_manager = WPCF7_ShortcodeManager::get_instance();
			$tag_manager->do_shortcode( $form_input );
			$form_tags = $tag_manager->get_scanned_tags();
		}

		if ( ! empty( $form_tags ) && is_array( $form_tags ) ) {
			foreach ( $form_tags as $key => $value ) {
				if ( ! empty( $value['name'] ) ) {
					$name = $value['name'];
					$val  = $cf7_submit->get_posted_data( $name );

					if ( ! isset( $form_entries[ $name ] ) ) {
						$form_entries[ $name ] = $val;
					}
				}
			}
		}

		return array(
			'id'     => $form_id,
			'name'   => $form_title,
			'fields' => $form_tags,
			'values' => $form_entries,
		);
	}

	/**
	 * Get all feeds of a respective form id.
	 *
	 * @param     int $form_id    Form id.
	 * @since     1.0.0
	 * @return    array
	 */
	public function get_feeds_by_form_id( $form_id = '' ) {

		if ( empty( $form_id ) ) {
			return;
		}

		// Get all feeds.
		$active_feeds = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'mwb_' . $this->crm_slug . '_cf7',
				'post_staus'  => 'publish',
				'order'       => 'DESC',
				'meta_query'  => array( // phpcs:ignore
					array(
						'relation' => 'AND',
						array(
							'key'     => 'mwb-' . $this->crm_slug . '-cf7-form',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'mwb-' . $this->crm_slug . '-cf7-form',
							'value'   => $form_id,
							'compare' => '==',
						),
						array(
							'key'     => 'mwb-' . $this->crm_slug . '-cf7-dependent-on',
							'compare' => 'NOT EXISTS',
						),
					),
				),
			)
		);

		return $active_feeds;
	}

	/**
	 * Check if filter exists in feed.
	 *
	 * @param     int $feed_id    Feed ID.
	 * @since     1.0.0
	 * @return    bool|array
	 */
	public function maybe_check_filter( $feed_id = '' ) {

		if ( empty( $feed_id ) ) {
			return;
		}
		if ( metadata_exists( 'post', $feed_id, 'mwb-' . $this->crm_slug . '-cf7-condtion-field' ) ) {
			$meta = get_post_meta( $feed_id, 'mwb-' . $this->crm_slug . '-cf7-condtion-field', true );

			if ( ! empty( $meta ) && is_array( $meta ) && count( $meta ) > 0 ) {
				return $meta;
			}
		}

		return false;
	}

	/**
	 * Validate form values with conditions.
	 *
	 * @param    String $option_type    Filter conditon type.
	 * @param    String $feed_value     Value to compare with entry value.
	 * @param    String $form_value     Entry value .
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public function is_value_allowed( $option_type = false, $feed_value = false, $form_value = false ) {

		if ( false == $option_type || false === $feed_value ) { // phpcs:ignore
			return;
		}

		$time   = current_time( 'timestamp' ); // phpcs:ignore
		$result = false;
		if ( false != $form_value ) { // phpcs:ignore

			switch ( $option_type ) {

				case 'exact_match':
					if ( $feed_value === $form_value ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'no_exact_match':
					if ( $feed_value !== $form_value ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'contains':
					if ( false !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_contains':
					if ( false === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'exist':
					if ( false !== strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_exist':
					if ( false === strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'starts':
					if ( 0 === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_starts':
					if ( 0 !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'ends':
					if ( strlen( $form_value ) == strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'not_ends':
					if ( strlen( $form_value ) != strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'less_than':
					if ( (float) $form_value < (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'greater_than':
					if ( (float) $form_value > (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'less_than_date':
					if ( strtotime( $form_value, $time ) < strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'greater_than_date':
					if ( strtotime( $form_value, $time ) > strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'equal_date':
					if ( strtotime( $form_value, $time ) == strtotime( $feed_value, $time ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'empty':
					if ( empty( $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_empty':
					if ( ! empty( $form_value ) ) {
						$result = true;
					}
					break;

				case 'only_number':
					if ( is_numeric( $form_value ) ) {
						$result = true;
					}
					break;

				case 'only_text':
					if ( preg_match( '/[^A-Za-z0-9]/', $form_value ) ) {
						$result = true;
					}
					break;

				default:
					$result = false;
					break;
			}
		}
		return $result;
	}
	// End of class.
}
