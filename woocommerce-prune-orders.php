<?php
/**
 * Plugin Name: WooCommerce Prune Orders
 * Plugin URI: https://github.com/Coded-Commerce-LLC/WooCommerce-Prune-Orders
 * Description: Adds a tool to the WooCommerce tools page which puts orders of selected status into the trash where they can be permanently deleted.
 * Version: 1.1
 * Author: Coded Commerce, LLC
 * Author URI: https://github.com/Coded-Commerce-LLC
 * WC requires at least: 1.0
 * WC tested up to: 3.4.5
 * License: GPLv2 or later
 * Text Domain: woocommerce-prune-orders
 */

// WordPress Or WooCommerce Hooks
add_action( 'admin_enqueue_scripts', [ 'woo_prune_orders', 'admin_enqueue_scripts' ] );
add_action( 'plugins_loaded',  [ 'woo_prune_orders', 'plugins_loaded' ] );
add_filter( 'woocommerce_debug_tools', [ 'woo_prune_orders', 'woocommerce_debug_tools' ] );

// Plugin Class
class woo_prune_orders {

	// Handle Tool Submissions
	static function run_tool() {
		global $wpdb;

		// Security Check
		if( ! current_user_can( 'manage_woocommerce' ) ) { return false; }

		// Ensure Action Provided
		if( empty( $_GET['action'] ) ) { return false; }

		// Map To WooCommerce Order Status
		$status_mappings = [
			'wc-cancelled' => 'prune_cancelled_orders',
			'wc-completed' => 'prune_completed_orders',
			'wc-failed' => 'prune_failed_orders',
			'wc-pending' => 'prune_pending_orders',
			'wc-refunded' => 'prune_refunded_orders',
		];
		$post_date = isset( $_GET['post_date'] ) ? $_GET['post_date'] : '';
		$post_status = array_search( $_GET['action'], $status_mappings );
		if( empty( $post_status ) || empty( $post_date ) ) { return false; }

		// Run Database Query
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"
					SELECT ID FROM $wpdb->posts
					WHERE post_type = 'shop_order'
					AND post_status = %s
					AND post_date <= %s
				",
				$post_status,
				date( 'Y-m-d H:i:s', strtotime( $post_date ) )
			)
		);

		// Send Result Posts To Trash
		foreach( $rows as $post_id ) {
			wp_trash_post( $post_id );
		}

		// Response
		$msg_singular = __( 'order was moved to the trash.', 'woocommerce-prune-orders' );
		$msg_plural = __( 'orders were moved to the trash.', 'woocommerce-prune-orders' );
		$message = sizeof( $rows ) === 1 ? $msg_singular : $msg_plural;
		return sizeof( $rows ) . ' ' . $message;
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

	// jQuery For Tools Page
	static function admin_enqueue_scripts( $page ) {
		if( $page != 'woocommerce_page_wc-status' ) { return; }
		wp_enqueue_script(
			'woocommerce-prune-orders', plugin_dir_url( __FILE__ ) . 'woocommerce-prune-orders.js'
		);
	}

	// Load Translations
	static function plugins_loaded() {
		load_plugin_textdomain(
			'woocommerce-prune-orders', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}
}
