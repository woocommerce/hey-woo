<?php
/**
 * Integration tests for AnthropicClient.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

use WooCommerce\HeyWoo\Difm\AnthropicClient;

/**
 * Tests for AnthropicClient.
 */
class Test_Anthropic_Client extends WP_UnitTestCase {

	/**
	 * Restore API key options after each test.
	 *
	 * @return void
	 */
	public function tear_down() {
		delete_option( 'hey_woo_anthropic_api_key' );
		delete_option( 'woocommerce_claude_anthropic_api_key' );
		remove_all_filters( 'pre_http_request' );

		parent::tear_down();
	}

	/**
	 * Returns an empty string when no key is configured.
	 */
	public function test_get_api_key_returns_empty_string_by_default() {
		$this->assertSame( '', AnthropicClient::get_api_key() );
		$this->assertFalse( AnthropicClient::has_api_key() );
	}

	/**
	 * Hey Woo's own BYOK option wins.
	 */
	public function test_get_api_key_returns_hey_woo_option_value() {
		update_option( 'hey_woo_anthropic_api_key', 'sk-ant-hey-woo-key' );
		update_option( 'woocommerce_claude_anthropic_api_key', 'sk-ant-legacy-key' );

		$this->assertSame( 'sk-ant-hey-woo-key', AnthropicClient::get_api_key() );
		$this->assertTrue( AnthropicClient::has_api_key() );
	}

	/**
	 * The legacy WooCommerce for Claude option remains a fallback.
	 */
	public function test_get_api_key_uses_legacy_option_as_fallback() {
		update_option( 'woocommerce_claude_anthropic_api_key', 'sk-ant-legacy-key' );

		$this->assertSame( 'sk-ant-legacy-key', AnthropicClient::get_api_key() );
	}

	/**
	 * Messages() returns WP_Error when no key is configured.
	 */
	public function test_messages_returns_wp_error_without_key() {
		$client = new AnthropicClient();
		$result = $client->messages(
			array(
				array(
					'role'    => 'user',
					'content' => 'Hello',
				),
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'no_api_key', $result->get_error_code() );
	}

	/**
	 * Messages() returns the decoded Anthropic response on success.
	 */
	public function test_messages_returns_decoded_response_on_success() {
		update_option( 'hey_woo_anthropic_api_key', 'sk-ant-test' );

		add_filter(
			'pre_http_request',
			static function () {
				return array(
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'body'     => wp_json_encode(
						array(
							'id'      => 'msg_123',
							'type'    => 'message',
							'content' => array(
								array(
									'type' => 'text',
									'text' => 'Hello!',
								),
							),
						)
					),
					'headers'  => array(),
				);
			},
			10,
			3
		);

		$client = new AnthropicClient();
		$result = $client->messages(
			array(
				array(
					'role'    => 'user',
					'content' => 'Hello',
				),
			)
		);

		$this->assertIsArray( $result );
		$this->assertSame( 'msg_123', $result['id'] );
	}
}
