<?php
/**
 * Pricing discount metabox options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @version 1.4.1
 * @author  YITH
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$key                   = uniqid();
$discount_pricing_mode = ywdpd_discount_pricing_mode();
$last_priority         = ywdpd_get_last_priority( 'pricing' ) + 1;
$pricing_rules_options = YITH_WC_Dynamic_Pricing()->pricing_rules_options;

return apply_filters(
	'ywdpd_pricing_discount_metabox_options',
	array(
		'label'    => __( 'Pricing Discount Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		'pages'    => 'ywdpd_discount', // or array( 'post-type1', 'post-type2')
		'context'  => 'normal', // ('normal', 'advanced', or 'side')
		'priority' => 'default',
		'tabs'     => array(

			'settings' => array(
				'label'  => __( 'Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'fields' => apply_filters(
					'ywdpd_pricing_discount_metabox',
					array(
						'discount_type'            => array(
							'type' => 'hidden',
							'std'  => 'pricing',
							'val'  => 'pricing',
						),
						'key'                      => array(
							'type' => 'hidden',
							'std'  => $key,
							'val'  => $key,
						),
						'active'                   => array(
							'label' => __( 'Active', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'desc'  => __( 'Choose if activate or deactivate', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'type'  => 'onoff',
							'std'   => 'yes',
						),
						// @since 1.1.0
						'discount_mode'            => array(
							'label'   => __( 'Discount mode', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'desc'    => '',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => array(
								'bulk' => __( 'Quantity Discount', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							),
							'std'     => 'bulk',
						),

						'priority'                 => array(
							'label' => __( 'Priority', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'desc'  => '',
							'type'  => 'text',
							'std'   => $last_priority,
						),

						/***************
						 * APPLY TO
						 */
						'apply_to'                 => array(
							'label'   => __( 'Apply to', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'desc'    => __( 'Select the products to which applying the rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => $pricing_rules_options['apply_to'],
							'std'     => 'all_products',
						),
						'apply_to_products_list'   => array(
							'label'       => __( 'Search for a product', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'type'        => 'products',
							'desc'        => '',
							'placeholder' => __( 'Search for a product', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'products_list',
							),
						),

						'apply_to_categories_list' => array(
							'label'       => __( 'Search for a category', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'type'        => 'categories',
							'desc'        => '',
							'placeholder' => __( 'Search for a category', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'deps'        => array(
								'ids'    => '_apply_to',
								'values' => 'categories_list',
							),
						),

						/***************
						 * DISCOUNT TABLES
						 */
						'rules'                    => array(
							'label'   => __( 'Discount Rules', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
							'desc'    => '',
							'type'    => 'quantity_discount',
							'private' => false,
							'deps'    => array(
								'ids'    => '_discount_mode',
								'values' => 'bulk',
							),
						),

					)
				),

			),
		),
	)
);
