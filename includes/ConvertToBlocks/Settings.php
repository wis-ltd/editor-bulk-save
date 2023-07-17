<?php
/**
 * Settings UI
 *
 * @package convert-to-blocks
 */

namespace ConvertToBlocks;

/**
 * UI for configuring plugin settings.
 */
class Settings {

	/**
	 * User permissions to manage settings.
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	private $settings_page = 'settings-page';

	/**
	 * Settings section name.
	 *
	 * @var string
	 */
	private $settings_section = 'settings-section';

	/**
	 * Settings group name.
	 *
	 * @var string
	 */
	private $settings_group = 'settings';

	/**
	 * Post types.
	 *
	 * @var array
	 */
	private $post_types = [];

	/**
	 * Register hooks with WordPress
	 */
	public function register() {
		// Configure variables and get post types.
		$this->init();

		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_notices', [ $this, 'filter_notice' ], 10 );
	}

	/**
	 * Only registers on admin context.
	 */
	public function can_register() {
		return is_admin();
	}

	/**
	 * Configures variables and fetches post types.
	 *
	 * @return void
	 */
	public function init() {
		// Configure variables.
		$this->settings_page    = sprintf( '%s-%s', CONVERT_TO_BLOCKS_SLUG, $this->settings_page );
		$this->settings_section = sprintf( '%s-%s', CONVERT_TO_BLOCKS_SLUG, $this->settings_section );
		$this->settings_group   = sprintf( '%s_%s', CONVERT_TO_BLOCKS_SLUG, $this->settings_group );

		// Get post types.
		$this->post_types = $this->get_post_types();
	}

	/**
	 * Retrieves all public post types.
	 *
	 * @return array
	 */
	public function get_post_types() {
		$post_types = get_post_types(
			[ 'public' => true ]
		);

		if ( ! empty( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		return $post_types;
	}

	/**
	 * Adds a submenu item for the `Settings` menu.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Editor bulk save', 'convert-to-blocks' ),
			esc_html__( 'Editor bulk save', 'convert-to-blocks' ),
			$this->capability,
			CONVERT_TO_BLOCKS_SLUG,
			[ $this, 'settings_page' ]
		);
	}

	/**
	 * Registers the settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Editor bulk save', 'convert-to-blocks' ); ?>
			</h1>
			<hr>

			<form method="post" action="options.php" class="js--start-bulk-update">
				<?php
				submit_button('Bulk save');
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Adds an admin notice if a filter is active for `post_type_supports_convert_to_blocks` as
	 * this might overwrite the outcome of the settings stored in DB.
	 */
	public function filter_notice() {
		if ( ! has_filter( 'post_type_supports_convert_to_blocks' ) ) {
			return;
		}

		$show_on_pages = array(
			'settings_page_convert-to-blocks',
			'plugins',
		);

		$screen = get_current_screen();

		if ( is_null( $screen ) ) {
			return;
		}

		if ( ! ( ! empty( $screen->post_type ) || in_array( $screen->id, $show_on_pages, true ) ) ) {
			return;
		}

		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					esc_html__( 'A filter hook (post_type_supports_convert_to_blocks) is already active.', 'convert-to-blocks' ),
				);
				?>
			</p>
		</div>
		<?php
	}

}
