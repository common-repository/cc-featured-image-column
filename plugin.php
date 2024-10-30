<?php

/**
 * CC-Featured-Image-Column
 *
 * @package     CC-Featured-Image-Column
 * @author      PiotrPress
 * @copyright   2018 Clearcode
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: CC-Featured-Image-Column
 * Plugin URI:  https://wordpress.org/plugins/cc-featured-image-column
 * Description: This plugin adds a column with post's featured image before the title column on wp-admin posts list.
 * Version:     1.0.0
 * Author:      Clearcode
 * Author URI:  https://clearcode.cc
 * Text Domain: cc-featured-image-column
 * Domain Path: /
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt

   Copyright (C) 2018 by Clearcode <https://clearcode.cc>
   and associates (see AUTHORS.txt file).

   This file is part of CC-Featured-Image-Column.

   CC-Featured-Image-Column is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   CC-Featured-Image-Column is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CC-Featured-Image-Column; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined( 'ABSPATH' ) or exit;

add_action( 'admin_init', function() {
	foreach( [ 'post', 'page' ] + get_post_types( [ '_builtin' => false ] ) as $post_type ) {
		if ( ! post_type_supports( $post_type, 'thumbnail' ) ) continue;
		if ( ! current_theme_supports( 'post-thumbnails', $post_type ) ) continue;
		add_filter( "manage_{$post_type}_posts_columns", function ( $columns ) {
			return array_slice( $columns, 0, 1 ) + [ 'featured-image' => __( 'Image', 'cc-featured-image-column' ) ] + array_slice( $columns, 1 );
		}, 10, 1 );
		add_action( "manage_{$post_type}_posts_custom_column", function ( $column, $post_id ) {
			if ( 'featured-image' !== $column ) return;
			if ( has_post_thumbnail( $post_id ) ) echo get_the_post_thumbnail( $post_id );
			else echo  '<img alt="' . esc_attr( get_the_title( $post_id ) ) . ' "src="' . esc_url( apply_filters( 'cc-featured-image-column/default', plugin_dir_url( __FILE__ ) . 'default.png' ) ) . '" />';
		}, 10, 2 );
	}
	add_action( 'admin_enqueue_scripts', function( $page ) {
		if ( 'edit.php' !== $page ) return;
		wp_register_style( 'cc-featured-image-column', plugin_dir_url( __FILE__ ) . 'style.css', [], '1.0.0' );
		wp_enqueue_style(  'cc-featured-image-column' );
	}, 10, 1 );
}, 100 );
