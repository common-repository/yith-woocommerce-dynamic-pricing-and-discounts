<?php
/**
 * Pricing rules options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @version 1.4.1
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

return apply_filters(
	'yit_ywdpd_pricing_rules_options',
	array(
		'discount_mode'    => array(
			'bulk' => __( 'Quantity Discount', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		),

		'quantity_based'   => array(
			'cart_line'                => __( 'Item quantity in cart line', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'single_product'           => __( 'Single product', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'single_variation_product' => __( 'Single product variation', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'cumulative'               => __( 'Sum of all products in list or category list', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		),

		'apply_to'         => array(
			'all_products'    => __( 'All products', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'categories_list' => __( 'Include a list of categories', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		),


		'type_of_discount' => array(
			'percentage' => __( 'Percentage Discount', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		),

	)
);
