<?php
/**
 * General options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @version 1.4.1
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$settings = array(

	'general' => array(

		'header'   => array(

			array(
				'name' => __( 'General Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'type' => 'title',
			),

			array( 'type' => 'close' ),
		),


		'settings' => array(

			array( 'type' => 'open' ),

			array(
				'id'   => 'enabled',
				'name' => __( 'Enable Dynamic Pricing and Discounts', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
				'desc' => '',
				'type' => 'onoff',
				'std'  => 'yes',
			),

			array( 'type' => 'close' ),
		),
	),
);

return apply_filters( 'yith_ywdpd_panel_settings_options', $settings );
