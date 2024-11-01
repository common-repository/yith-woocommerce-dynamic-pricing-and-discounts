<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */

if ( ! function_exists( 'ywdpd_get_shop_categories' ) ) {
	function ywdpd_get_shop_categories( $show_all = true ) {
		global $wpdb;

		$terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_cat" ORDER BY name ASC;' );

		$categories = array();
		if ( $show_all ) {
			$categories['0'] = __( 'All categories', 'ywcm' );
		}
		if ( $terms ) {
			foreach ( $terms as $cat ) {
				$categories[ $cat->term_id ] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
			}
		}
		return $categories;
	}
}

if ( ! function_exists( 'yith_ywdpd_check_update_to_cpt_free' ) ) {

	/**
	 * Check if is necessary transform the rules from option to cpt
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_ywdpd_check_update_to_cpt_free() {
		$ywdpd_updated_to_cpt = get_option( 'ywdpd_updated_to_cpt' );
		$options              = get_option( 'yit_ywdpd_options' );
		$options              = empty( $options ) ? get_option( 'yit_yith-woocommerce-dynamic-pricing-and-discounts_options' ) : $options;
		if ( ! ywdpd_is_true( $ywdpd_updated_to_cpt ) && ! empty( $options ) ) {
			yith_ywdpd_update_to_cpt_free();
		}
	}
}

if ( ! function_exists( 'yith_ywdpd_update_to_cpt_free' ) ) {

	/**
	 * Transforms the old rules in Custom post type
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_ywdpd_update_to_cpt_free() {
		$option_types = array( 'pricing' );
		$options      = get_option( 'yit_ywdpd_options' );
		$options      = empty( $options ) ? get_option( 'yit_yith-woocommerce-dynamic-pricing-and-discounts_options' ) : $options;

		$args = array(
			'post_type'      => 'ywdpd_discount',
			'comment_status' => 'closed',
			'post_status'    => 'publish',
		);

		if ( $options ) {
			foreach ( $option_types as $type ) {
				$priority = 0;

				if ( isset( $options[ $type . '-rules' ] ) ) {
					$rules      = $options[ $type . '-rules' ];
					$rule_array = array();
					foreach ( $rules as $key => $value ) {
						$priority ++;
						$args['post_title'] = $value['description'];

						$rule = array(
							'min_quantity'    => '',
							'max_quantity'    => '*',
							'type_discount'   => 'percentage',
							'discount_amount' => 0,
						);

						$id = wp_insert_post( $args );
						if ( $id ) {
							add_post_meta( $id, '_key', $key );
							add_post_meta( $id, '_discount_type', $type );
							add_post_meta( $id, '_priority', $priority );

							foreach ( $value as $key => $item ) {

								if ( 'apply_to' == $key && 'categories' == $item ) {
									$item = 'categories_list';
								}

								if ( 'apply_to' == $key && 'all-products' == $item ) {
									$item = 'all_products';
								}

								if ( 'categories' == $key ) {
									$key = 'apply_to_categories_list';
								}

								if ( 'min_quantity' == $key ) {
									$rule['min_quantity'] = $item;
									continue;
								}

								if ( 'discount_amount' == $key ) {
									$rule['discount_amount'] = $item;
									continue;
								}

								$meta_key = '_' . $key;

								add_post_meta( $id, $meta_key, $item );

							}
							$rule_array[1] = $rule;
							add_post_meta( $id, 'rules', $rule_array );

						}
					}
				}
			}

			update_option( 'ywdpd_updated_to_cpt', 'yes' );
		}
	}
}

if ( ! function_exists( 'ywdpd_recover_rules' ) ) {

	/**
	 * @param $type
	 *
	 * @return array
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_recover_rules( $type ) {
		$args = array(
			'post_type'      => 'ywdpd_discount',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_discount_type',
					'value' => $type,
				),
			),
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_priority',
			'order'          => 'ASC',
		);

		$posts = get_posts( $args );
		$rules = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$metas = get_post_meta( $post->ID );

				if ( $metas ) {
					$rule = array();
					foreach ( $metas as $key => $meta_value ) {
						$new_key          = ywdpd_maybe_remove_prefix_key( $key );
						$rule[ $new_key ] = ywdpd_format_meta_value( reset( $meta_value ), $new_key );
					}

					if ( $type == 'cart' && isset( $rule['discount_rule'] ) ) {
						$rule['discount_amount'] = isset( $rule['discount_rule']['discount_amount'] ) ? $rule['discount_rule']['discount_amount'] : '';
						$rule['discount_type']   = isset( $rule['discount_rule']['discount_type'] ) ? $rule['discount_rule']['discount_type'] : '';
					}
				}

				if ( isset( $rule['key'] ) ) {
					$rules[ $rule['key'] ] = $rule;
				}
			}
		}

		return $rules;
	}
}

if ( ! function_exists( 'ywdpd_format_meta_value' ) ) {
	/**
	 * @param $value
	 *
	 * @return int|mixed
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_format_meta_value( $value, $key ) {
		$value = maybe_unserialize( $value );

		if ( $value == 'yes' && $key != 'active' ) {
			$value = 1;
		} elseif ( $key == 'active' && $value == 1 ) {
			$value = 'yes';
		}

		return $value;

	}
}

if ( ! function_exists( 'ywdpd_maybe_remove_prefix_key' ) ) {
	/**
	 * Remove the char '_' from a word
	 *
	 * @param $key
	 *
	 * @return bool|string
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_maybe_remove_prefix_key( $key ) {
		return '_' === substr( $key, 0, 1 ) ? substr( $key, 1 ) : $key;
	}
}

if ( ! function_exists( 'ywdpd_get_last_priority' ) ) {

	/**
	 * Returns the last priority
	 *
	 * @param $type
	 *
	 * @return int|mixed
	 *
	 * @since 1.2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_get_last_priority( $type ) {

		$args = array(
			'post_type'      => 'ywdpd_discount',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => '_discount_type',
					'value' => $type,
				),
			),
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_priority',
			'order'          => 'DESC',
		);

		$posts = new WP_Query( $args );

		return ( $posts->post ) ? get_post_meta( $posts->post->ID, '_priority', true ) : 1;

	}
}

if ( ! function_exists( 'ywdpd_check_valid_admin_page' ) ) {
	/**
	 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type
	 *
	 * @param $post_type_name
	 *
	 * @return bool
	 * @author Emanuela Castorina
	 */
	function ywdpd_check_valid_admin_page( $post_type_name ) {
		global $pagenow;

		$posted = $_REQUEST;
		$post   = isset( $posted['post'] ) ? $posted['post'] : ( isset( $posted['post_ID'] ) ? $posted['post_ID'] : 0 );
		$post   = get_post( $post );

		if ( ( $post && $post->post_type === $post_type_name ) || ( isset( $posted['post_type'] ) && $post_type_name === $posted['post_type'] ) ) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'ywdpd_discount_pricing_mode' ) ) {

	/**
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_discount_pricing_mode() {

		return array(
			'bulk'          => __( 'Quantity Discount', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'special_offer' => __( 'Special Offer', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
			'exclude_items' => __( 'Exclude items from rules', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
		);
	}
}

if ( ! function_exists( 'ywdpd_is_true' ) ) {
	function ywdpd_is_true( $value ) {
		return true === $value || 1 === $value || '1' === $value || 'yes' === $value;
	}
}
