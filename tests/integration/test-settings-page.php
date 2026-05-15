<?php
/**
 * Integration tests for the Hey Woo settings page.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

use WooCommerce\HeyWoo\Settings\SettingsPage;

/**
 * Tests for SettingsPage API-key handling.
 */
class Test_Settings_Page extends WP_UnitTestCase {

	/**
	 * Settings page instance under test.
	 *
	 * @var SettingsPage|null
	 */
	private $settings_page = null;

	/**
	 * Tear down persisted options and globals.
	 *
	 * @return void
	 */
	public function tear_down() {
		delete_option( SettingsPage::DIFM_API_KEY_OPTION );
		delete_option( SettingsPage::LEGACY_DIFM_API_KEY_OPTION );
		remove_all_filters( 'pre_http_request' );
		$_POST = array();

		if ( $this->settings_page ) {
			remove_action( 'woocommerce_admin_field_hey_woo_api_key', array( $this->settings_page, 'render_api_key_field' ) );
			remove_filter( 'woocommerce_admin_settings_sanitize_option_' . SettingsPage::DIFM_API_KEY_OPTION, array( $this->settings_page, 'sanitize_api_key_option' ), 10 );
			remove_action( 'woocommerce_settings_save_hey-woo', array( $this->settings_page, 'validate_api_key_on_save' ) );
		}

		parent::tear_down();
	}

	/**
	 * A saved key is masked in rendered settings HTML.
	 */
	public function test_saved_api_key_is_masked_in_rendered_settings_html() {
		update_option( SettingsPage::DIFM_API_KEY_OPTION, 'sk-ant-real-secret', 'no' );

		$html = $this->render_settings_html();

		$this->assertStringNotContainsString( 'sk-ant-real-secret', $html );
		$this->assertStringContainsString( SettingsPage::DIFM_API_KEY_SENTINEL, $html );
		$this->assertStringContainsString( 'type="password"', $html );
	}

	/**
	 * The Hey Woo tab exposes a single settings section.
	 */
	public function test_sections_include_settings() {
		$this->assertSame(
			array(
				'' => 'Settings',
			),
			$this->settings_page()->get_sections()
		);
	}

	/**
	 * Blank submissions preserve an existing key.
	 */
	public function test_blank_save_preserves_existing_key() {
		update_option( SettingsPage::DIFM_API_KEY_OPTION, 'sk-ant-existing', 'no' );

		$this->assertSame(
			'sk-ant-existing',
			$this->settings_page()->sanitize_api_key_option( '', array(), '' )
		);
	}

	/**
	 * Sentinel submissions preserve an existing key.
	 */
	public function test_sentinel_save_preserves_existing_key() {
		update_option( SettingsPage::DIFM_API_KEY_OPTION, 'sk-ant-existing', 'no' );

		$this->assertSame(
			'sk-ant-existing',
			$this->settings_page()->sanitize_api_key_option( SettingsPage::DIFM_API_KEY_SENTINEL, array(), SettingsPage::DIFM_API_KEY_SENTINEL )
		);
	}

	/**
	 * Clear submissions delete both current and legacy options.
	 */
	public function test_clear_submission_removes_current_and_legacy_keys() {
		update_option( SettingsPage::DIFM_API_KEY_OPTION, 'sk-ant-existing', 'no' );
		update_option( SettingsPage::LEGACY_DIFM_API_KEY_OPTION, 'sk-ant-legacy', 'no' );

		$_POST = array(
			SettingsPage::DIFM_API_KEY_CLEAR_FIELD => 'yes',
		);

		$this->assertNull( $this->settings_page()->sanitize_api_key_option( '', array(), '' ) );
		$this->assertSame( '', get_option( SettingsPage::DIFM_API_KEY_OPTION, '' ) );
		$this->assertSame( '', get_option( SettingsPage::LEGACY_DIFM_API_KEY_OPTION, '' ) );
	}

	/**
	 * Render the settings field HTML.
	 *
	 * @return string
	 */
	private function render_settings_html() {
		ob_start();
		$this->settings_page()->render_api_key_field(
			array(
				'id'    => SettingsPage::DIFM_API_KEY_OPTION,
				'title' => 'Anthropic API key',
				'desc'  => 'Test description.',
			)
		);
		return ob_get_clean();
	}

	/**
	 * Return the settings page under test.
	 *
	 * @return SettingsPage
	 */
	private function settings_page() {
		if ( ! $this->settings_page ) {
			$this->settings_page = new SettingsPage();
		}

		return $this->settings_page;
	}
}
