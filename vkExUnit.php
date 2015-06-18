<?php
/*
Plugin Name: VK All in one Expansion Unit
Plugin URI: http://vektor-inc.co.jp
Description: 
Version: 0.0.0.0
Author: Vektor,Inc,
Author URI: http://vektor-inc.co.jp
License: GPL2
*/
/*
Copyright 2015 Hidekazu Ishikawa ( email : kurudrive@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*-------------------------------------------*/
/*	Load master setting page
/*-------------------------------------------*/
/*	Load modules
/*-------------------------------------------*/
/*	Add Parent menu
/*-------------------------------------------*/
/*	Add vkExUnit css
/*-------------------------------------------*/


function vkExUnit_get_directory(){
	return $dirctory = dirname( __FILE__ );
}



add_action('wp_head','vkExUnit_addJs');
function vkExUnit_addJs(){
	wp_register_script( 'vkExUnit_master-js' , plugins_url('', __FILE__).'/js/master.js', array('jquery'), '20150525' );
	wp_enqueue_script( 'vkExUnit_master-js' );
}



/*-------------------------------------------*/
/*	Add Parent menu
/*-------------------------------------------*/
add_action( 'admin_menu', 'vkExUnit_setting_menu_parent' );
function vkExUnit_setting_menu_parent() {
	global $menu;
	$parent_name = 'VK Ex Unit';
	$Capability_required = 'activate_plugins';

	$custom_page = add_menu_page(
		$parent_name,				// Name of page
		$parent_name,				// Label in menu
		$Capability_required,
		'vkExUnit_setting_page',	// ユニークなこのサブメニューページの識別子
		'vkExUnit_add_setting_page'	// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) return;
}

/*-------------------------------------------*/
/*	Load master setting page
/*-------------------------------------------*/
function vkExUnit_add_setting_page(){
	require dirname( __FILE__ ) . '/vkExUnit_admin.php';
}

/*-------------------------------------------*/
/*	Load modules
/*-------------------------------------------*/

require vkExUnit_get_directory() . '/common_init.php';
$options = vkExUnit_get_common_options();
require vkExUnit_get_directory() . '/common_helpers.php';

require vkExUnit_get_directory() . '/plugins/sitemap_page/sitemap.php';

if ( isset($options['active_sns']) && $options['active_sns'] )
	require vkExUnit_get_directory() . '/plugins/sns/sns_common.php';

if ( isset($options['active_ga']) && $options['active_ga'] )
	require vkExUnit_get_directory() . '/plugins/google_analytics/ga.php';

if ( isset($options['active_relatedPosts']) && $options['active_relatedPosts'] )
	require vkExUnit_get_directory() . '/plugins/related_posts/related_posts.php';

if ( isset($options['active_metaDescription']) && $options['active_metaDescription'] )
	require vkExUnit_get_directory() . '/plugins/meta_description/meta_description.php';

if ( isset($options['active_otherWidgets']) && $options['active_otherWidgets'] )
	require vkExUnit_get_directory() . '/plugins/other_widget/widget.php';

/*-------------------------------------------*/
/*	Add vkExUnit css
/*-------------------------------------------*/
// Add vkExUnit css
add_action('wp_enqueue_scripts','vkExUnit_print_css');
function vkExUnit_print_css(){
	wp_enqueue_style('vkExUnit_common_style', plugins_url('', __FILE__).'/css/style.css', array(), '20150525', 'all');
}

/*-------------------------------------------*/
/*	Print theme_options js
/*-------------------------------------------*/
add_action('admin_print_scripts-vk-ex-unit_page_vkExUnit_sns_options_page', 'vkExUnit_admin_add_js');
function vkExUnit_admin_add_js( $hook_suffix ) {
	wp_enqueue_media();
	wp_register_script( 'vkExUnit_admin_js', plugins_url('', __FILE__).'/js/vkExUnit_admin.js', array('jquery'), '20150525' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'vkExUnit_admin_js' );
}
