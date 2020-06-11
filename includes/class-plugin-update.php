<?php
/**
 * Handles option changes on plugin updates.
 *
 * @package     GenerateBlocks
 * @since       1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Process option updates if necessary.
 */
class GenerateBlocks_Plugin_Update {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @var $instance Class instance.
	 */
	private static $instance;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', __CLASS__ . '::init', 5 );
		} else {
			add_action( 'wp', __CLASS__ . '::init', 5 );
		}
	}

	/**
	 * Implement plugin update logic.
	 *
	 * @since 1.1.0
	 */
	public static function init() {
		$saved_version = get_option( 'generateblocks_version', false );

		if ( false === $saved_version ) {
			if ( 'admin_init' === current_action() ) {
				// If we're in the admin, add our version to the database.
				update_option( 'generateblocks_version', GENERATEBLOCKS_VERSION );
			}

			// Not an existing install, so no need to proceed further.
			return;
		}

		if ( version_compare( $saved_version, GENERATEBLOCKS_VERSION, '=' ) ) {
			return;
		}

		// Nothing to update yet.

		// Last thing to do is update our version.
		update_option( 'generateblocks_version', GENERATEBLOCKS_VERSION );
	}
}

GenerateBlocks_Plugin_Update::get_instance();
