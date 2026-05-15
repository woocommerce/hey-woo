<?php
/**
 * Integration tests for the vendored commerce-abilities package.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

/**
 * Tests for commerce-abilities bootstrap.
 */
class Test_Commerce_Abilities_Bootstrap extends WP_UnitTestCase {

	/**
	 * The vendored package classes are loaded by Hey Woo.
	 */
	public function test_commerce_abilities_classes_are_loaded() {
		$this->assertTrue( class_exists( \WooCommerce\CommerceAbilities\Loader::class ) );
		$this->assertTrue( class_exists( \WooCommerce\CommerceAbilities\Abilities\AnalyticsBootstrap::class ) );
		$this->assertTrue( class_exists( \WooCommerce\CommerceAbilities\Analytics\AnalyticsService::class ) );
	}

	/**
	 * The shared analytics abilities register through WordPress Abilities.
	 */
	public function test_shared_analytics_abilities_register() {
		$this->assertTrue( function_exists( 'wp_has_ability' ) );
		$this->assertTrue( wp_has_ability( 'wc-analytics/totals' ) );
		$this->assertTrue( wp_has_ability( 'wc-analytics/breakdown' ) );
		$this->assertTrue( wp_has_ability( 'wc-analytics/series' ) );
		$this->assertTrue( wp_has_ability( 'wc-analytics/rows' ) );
	}
}
