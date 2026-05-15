<?php
/**
 * Integration tests for DifmRestController.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

use WooCommerce\HeyWoo\Difm\DifmRestController;

/**
 * Tests for DifmRestController.
 */
class Test_Difm_Rest_Controller extends WP_UnitTestCase {

	/**
	 * REST server instance used for dispatching test requests.
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	/**
	 * Set up a REST server for each test.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment -- This action is documented in WordPress core.
		do_action( 'rest_api_init' );
	}

	/**
	 * Clean up options and HTTP filters.
	 *
	 * @return void
	 */
	public function tear_down() {
		delete_option( 'hey_woo_anthropic_api_key' );
		remove_all_filters( 'pre_http_request' );

		parent::tear_down();
	}

	/**
	 * The Hey Woo chat route is registered.
	 */
	public function test_chat_route_is_registered() {
		$routes = $this->server->get_routes();

		$this->assertArrayHasKey( '/hey-woo/v1/difm/chat', $routes );
	}

	/**
	 * The DIFM tool allowlist remains curated.
	 */
	public function test_tool_allowlist_matches_expected_abilities() {
		$map = DifmRestController::get_tool_ability_map();

		$this->assertSame(
			array(
				'analytics_totals',
				'analytics_breakdown',
				'analytics_series',
				'analytics_rows',
				'get_product_details',
				'search_products',
				'get_store_profile',
				'get_readiness_score',
				'get_recommendations',
				'suggest_improvements',
			),
			array_keys( $map )
		);
		$this->assertArrayNotHasKey( 'confirm_large_range', $map );
	}

	/**
	 * Unauthenticated chat requests receive a 403.
	 */
	public function test_chat_requires_manage_woocommerce() {
		$request = new WP_REST_Request( 'POST', '/hey-woo/v1/difm/chat' );
		$request->set_param( 'message', 'Hello' );
		$response = $this->server->dispatch( $request );

		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * Chat returns no_key when the merchant has not configured Anthropic.
	 */
	public function test_chat_returns_no_key_when_unconfigured() {
		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'administrator' ) ) );

		$request = new WP_REST_Request( 'POST', '/hey-woo/v1/difm/chat' );
		$request->set_param( 'message', 'Hello' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 'no_key', $data['status'] );
	}

	/**
	 * Chat returns a mocked Anthropic reply when configured.
	 */
	public function test_chat_returns_reply_on_success() {
		update_option( 'hey_woo_anthropic_api_key', 'sk-ant-test' );
		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'administrator' ) ) );

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
							'type'    => 'message',
							'content' => array(
								array(
									'type' => 'text',
									'text' => 'Hello! How can I help?',
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

		$request = new WP_REST_Request( 'POST', '/hey-woo/v1/difm/chat' );
		$request->set_param( 'message', 'Hi there' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 'ok', $data['status'] );
		$this->assertSame( 'Hello! How can I help?', $data['reply'] );
	}
}
