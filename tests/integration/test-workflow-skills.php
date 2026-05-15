<?php
/**
 * Integration tests for WorkflowSkills.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

use WooCommerce\HeyWoo\Difm\WorkflowSkills;

/**
 * Tests for WorkflowSkills.
 */
class Test_Workflow_Skills extends WP_UnitTestCase {

	/**
	 * Workflow markdown files are discoverable.
	 */
	public function test_workflow_files_are_loaded() {
		$workflows = WorkflowSkills::all();

		$this->assertArrayHasKey( 'weekly-store-review', $workflows );
		$this->assertArrayHasKey( 'refund-triage', $workflows );
		$this->assertNotEmpty( $workflows['weekly-store-review']['description'] );
		$this->assertNotEmpty( $workflows['weekly-store-review']['body'] );
	}

	/**
	 * Slash commands select an exact workflow.
	 */
	public function test_slash_command_selects_workflow() {
		$workflow = WorkflowSkills::select_for_message( '/hey-woo:refund-triage please' );

		$this->assertIsArray( $workflow );
		$this->assertSame( 'refund-triage', $workflow['slug'] );
		$this->assertSame( 'command', $workflow['match'] );
	}

	/**
	 * Natural language selects a workflow deterministically.
	 */
	public function test_intent_selects_workflow() {
		$workflow = WorkflowSkills::select_for_message( 'Can you review product performance?' );

		$this->assertIsArray( $workflow );
		$this->assertSame( 'product-performance-review', $workflow['slug'] );
		$this->assertSame( 'intent', $workflow['match'] );
	}

	/**
	 * Prompt text hides implementation names from merchant-facing context.
	 */
	public function test_prompt_for_difm_translates_mcp_tool_references() {
		$workflow = WorkflowSkills::get( 'weekly-store-review' );
		$this->assertIsArray( $workflow );

		$prompt = WorkflowSkills::prompt_for_difm( $workflow );

		$this->assertStringContainsString( 'Selected workflow: weekly-store-review', $prompt );
		$this->assertStringNotContainsString( 'MCP', $prompt );
		$this->assertStringNotContainsString( 'wp-abilities', $prompt );
	}
}
