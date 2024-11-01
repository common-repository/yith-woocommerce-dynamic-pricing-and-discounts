<?php
/*
Plugin Name: YITH WooCommerce Dynamic Pricing and Discounts
Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/
Description: <code><strong>YITH WooCommerce Dynamic Pricing and Discounts</code></strong> allows editing prices and enabling dynamic discounts in a simple, quick and intuitive way. Keeping a store without this features would be a serious mistake! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
Version: 1.4.8
Author: YITH
Author URI: https://yithemes.com/
Text Domain: yith-woocommerce-dynamic-pricing-and-discounts
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 4.6
*/

/*
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// This version can't be activate if premium version is active  ________________________________________
if ( defined( 'YITH_YWDPD_PREMIUM' ) ) {
    function yith_ywdpd_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e( 'You can\'t activate the free version of YITH WooCommerce Dynamic Pricing and Discounts while you are using the premium one.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
        </div>
    <?php
    }

    add_action( 'admin_notices', 'yith_ywdpd_install_free_admin_notice' );

    deactivate_plugins( plugin_basename( __FILE__ ) );
    return;
}

// Registration hook  ________________________________________
if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( !function_exists( 'yith_ywdpd_install_woocommerce_admin_notice' ) ) {
    function yith_ywdpd_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e( 'YITH WooCommerce Dynamic Pricing and Discounts is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
        </div>
    <?php
    }
}

// Define constants ________________________________________
if ( defined( 'YITH_YWDPD_VERSION' ) ) {
    return;
}else{
    define( 'YITH_YWDPD_VERSION', '1.4.8' );
}

if ( ! defined( 'YITH_YWDPD_FREE_INIT' ) ) {
    define( 'YITH_YWDPD_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_INIT' ) ) {
    define( 'YITH_YWDPD_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_FILE' ) ) {
    define( 'YITH_YWDPD_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWDPD_DIR' ) ) {
    define( 'YITH_YWDPD_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_URL' ) ) {
    define( 'YITH_YWDPD_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_ASSETS_URL' ) ) {
    define( 'YITH_YWDPD_ASSETS_URL', YITH_YWDPD_URL . 'assets' );
}

if ( ! defined( 'YITH_YWDPD_TEMPLATE_PATH' ) ) {
    define( 'YITH_YWDPD_TEMPLATE_PATH', YITH_YWDPD_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWDPD_INC' ) ) {
    define( 'YITH_YWDPD_INC', YITH_YWDPD_DIR . '/includes/' );
}

if ( ! defined( 'YITH_YWDPD_SUFFIX' ) ) {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    define( 'YITH_YWDPD_SUFFIX', $suffix );
}

if ( ! defined( 'YITH_YWDPD_SLUG' ) ) {
	define( 'YITH_YWDPD_SLUG', 'yith-woocommerce-dynamic-pricing-and-discounts' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWDPD_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_YWDPD_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_YWDPD_DIR  );


if ( ! function_exists( 'yith_ywdpd_install' ) ) {
    function yith_ywdpd_install() {

        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_ywdpd_install_woocommerce_admin_notice' );
        } else {
            do_action( 'yith_ywdpd_init' );
        }

	    if( function_exists( 'yith_ywdpd_check_update_to_cpt_free' ) ) {
		    yith_ywdpd_check_update_to_cpt_free();
	    }
    }

    add_action( 'plugins_loaded', 'yith_ywdpd_install', 11 );
}


function yith_ywdpd_constructor() {

    // Woocommerce installation check _________________________
    if ( !function_exists( 'WC' ) ) {
        function yith_ywdpd_install_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php esc_html_e( 'YITH WooCommerce Dynamic Pricing and Discounts is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
            </div>
        <?php
        }

        add_action( 'admin_notices', 'yith_ywdpd_install_woocommerce_admin_notice' );
        return;
    }

    // Load YWSL text domain ___________________________________
    load_plugin_textdomain( 'yith-woocommerce-dynamic-pricing-and-discounts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	if( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	require_once( YITH_YWDPD_INC . 'functions.yith-wc-dynamic-pricing.php' );
    require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-admin.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-frontend.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-helper.php' );
	require_once YITH_YWDPD_INC . 'admin/class.ywdpd-discount-post-type-admin.php';


	if ( is_admin() ) {
		YITH_WC_Dynamic_Pricing_Admin();
		YITH_WC_Dynamic_Discount_Post_Type_Admin();
	}
    YITH_WC_Dynamic_Pricing();
    YITH_WC_Dynamic_Pricing_Frontend();

}
add_action( 'yith_ywdpd_init', 'yith_ywdpd_constructor' );
