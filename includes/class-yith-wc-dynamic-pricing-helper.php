<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Helper function for YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Dynamic_Pricing_Helper' ) ) {

	class YITH_WC_Dynamic_Pricing_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Dynamic_Pricing
		 */

		protected static $instance;


		public $categories_counter = array();
		public $cart_categories    = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Dynamic_Pricing
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'load_counters' ), 98 );
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		/**
		 * Register post type
		 *
		 * @since  1.2.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function register_post_type() {

			$labels = array(
				'name'               => _x( 'Discount Rules', 'Post Type General Name', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'singular_name'      => _x( 'Discount Rule', 'Post Type Singular Name', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'menu_name'          => __( 'Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'all_items'          => __( 'All Discount Rules', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'view_item'          => __( 'View Discount Rules', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'add_new_item'       => __( 'Add New Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'add_new'            => __( 'Add New Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'edit_item'          => __( 'Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'update_item'        => __( 'Update Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'search_items'       => __( 'Search Discount Rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'not_found'          => __( 'Not found', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			);

			$args = array(
				'label'               => __( 'ywdpd_discount', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
			);

			register_post_type( 'ywdpd_discount', $args );

		}

		public function load_counters() {
			if ( empty( WC()->cart->cart_contents ) ) {
				return;
			}

			$this->reset_counters();

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				$product_id = $cart_item['product_id'];
				$quantity   = $cart_item['quantity'];

				$categories = wp_get_post_terms( $product_id, 'product_cat' );
				foreach ( $categories as $category ) {
					$this->categories_counter[ $category->term_id ] = isset( $this->categories_counter[ $category->term_id ] ) ?
						$this->categories_counter[ $category->term_id ] + $quantity : $quantity;

					$this->cart_categories[] = $category->term_id;
				}
			}
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		private function reset_counters() {
			$this->categories_counter = array();
			$this->cart_categories    = array();
		}


	}
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Helper class
 *
 * @return \YITH_WC_Dynamic_Pricing_Helper
 */
function YITH_WC_Dynamic_Pricing_Helper() {
	return YITH_WC_Dynamic_Pricing_Helper::get_instance();
}

YITH_WC_Dynamic_Pricing_Helper();
