<?php
/**
 * Plugin Name: WooCommerce Prune Orders
 * Plugin URI: https://github.com/Coded-Commerce-LLC/WooCommerce-Prune-Orders
 * Description: Adds a tool to the WooCommerce tools page which puts orders of selected status into the trash where they can be permanently deleted.
 * Version: 1.1
 * Author: Coded Commerce, LLC
 * Author URI: https://github.com/Coded-Commerce-LLC
 * WC requires at least: 1.0
 * WC tested up to: 3.4.3
 * License: GPLv2 or later
 * Text Domain: woocommerce-prune-orders
 */

// WordPress Or WooCommerce Hooks
add_filter( 'woocommerce_debug_tools', [ 'woo_prune_orders', 'woocommerce_debug_tools' ] );
add_action( 'plugins_loaded',  [ 'woo_prune_orders', 'plugins_loaded' ] );

// Plugin Class
class woo_prune_orders {

	// Handle Tool Submissions
	static function run_tool() {
		global $wpdb;

		// Security Check
		if( ! current_user_can( 'manage_woocommerce' ) ) { return; }

		// Ensure Action Provided
		if( empty( $_GET['action'] ) ) { return; }

		// Map To WooCommerce Order Status
		$status_mappings = [
			'wc-cancelled' => 'prune_cancelled_orders',
			'wc-completed' => 'prune_completed_orders',
			'wc-failed' => 'prune_failed_orders',
			'wc-on-hold' => 'prune_onhold_orders',
			'wc-pending' => 'prune_pending_orders',
			'wc-refunded' => 'prune_refunded_orders',
		];
		$post_status = array_search( $_GET['action'], $status_mappings );
		if( empty( $post_status ) ) { return; }

		// Run Database Query
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"
					SELECT ID FROM $wpdb->posts
					WHERE post_type = 'shop_order'
					AND post_status = %s
				",
				$post_status
			)
		);

		// Send Result Posts To Trash
		foreach( $rows as $post_id ) {
			wp_trash_post( $post_id );
		}

		// Response
		return wp_sprintf(
			"%d %s",
			sizeof( $rows ),
			__( 'orders were moved to the trash.', 'woocommerce-prune-orders' )
		);
		return true;
	}

	// Adds Tools To WooCommerce
	static function woocommerce_debug_tools( $tools ) {
		$tools['prune_cancelled_orders'] = [
			'button' => __( 'Trash Cancelled orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all Cancelled WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all Cancelled orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		$tools['prune_completed_orders'] = [
			'button' => __( 'Trash Completed orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all Completed WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all Completed orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		$tools['prune_failed_orders'] = [
			'button' => __( 'Trash Failed orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all Failed WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all Failed orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		/*
		$tools['prune_onhold_orders'] = [
			'button' => __( 'Trash On Hold orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all On Hold WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all On Hold orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		*/
		$tools['prune_pending_orders'] = [
			'button' => __( 'Trash Pending orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all Pending WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all Pending orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		$tools['prune_refunded_orders'] = [
			'button' => __( 'Trash Refunded orders', 'woocommerce-prune-orders' ),
			'callback' => [ 'woo_prune_orders', 'run_tool' ],
			'name' => __( 'Trash all Refunded WooCommerce orders', 'woocommerce-prune-orders' ),
			'desc' => sprintf(
				"<strong class='red'>%s</strong> %s %s",
				__( 'Caution!', 'woocommerce-prune-orders' ),
				__( 'This option will move all Refunded orders to the trash.', 'woocommerce-prune-orders' ),
				__( 'Are you sure?', 'woocommerce-prune-orders' )
			),
		];
		return $tools;
	}

	// Load Translations
	static function plugins_loaded() {
		load_plugin_textdomain( 'woocommerce-prune-orders', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}
