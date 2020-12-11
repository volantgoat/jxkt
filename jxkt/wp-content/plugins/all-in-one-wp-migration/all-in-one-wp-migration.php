<?php
/**
 * Plugin Name: All-in-One WP Migration
 * Plugin URI: https://servmask.com/
 * Description: Migration tool for all your blog data. Import or Export your blog content with a single click.
 * Author: ServMask
 * Author URI: https://servmask.com/
 * Version: 96.70
 * Text Domain: all-in-one-wp-migration
 * Domain Path: /languages
 * Network: True
 *
 * Copyright (C) 2014-2018 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

// Check SSL Mode
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) ) {
	$_SERVER['HTTPS'] = 'on';
}

// Plugin Basename
define( 'AI1WM_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

// Plugin Path
define( 'AI1WM_PATH', dirname( __FILE__ ) );

// Plugin Url
define( 'AI1WM_URL', plugins_url( '', AI1WM_PLUGIN_BASENAME ) );

// Plugin Storage Url
define( 'AI1WM_STORAGE_URL', plugins_url( 'storage', AI1WM_PLUGIN_BASENAME ) );

// Plugin Backups Url
define( 'AI1WM_BACKUPS_URL', content_url( 'ai1wm-backups', AI1WM_PLUGIN_BASENAME ) );

// Themes Absolute Path
define( 'AI1WM_THEMES_PATH', get_theme_root() );

// Include constants
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php';

// Include deprecated
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'deprecated.php';

// Include functions
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';

// Include exceptions
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'exceptions.php';

// Include loader
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'loader.php';

// =========================================================================
// = All app initialization is done in Ai1wm_Main_Controller __constructor =
// =========================================================================
$main_controller = new Ai1wm_Main_Controller();

// cmhello

function myplugin_init() {
  load_plugin_textdomain( 'all-in-one-wp-migration', false , dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'myplugin_init');

function cmp_remove_import_bottons(){
	return array( Ai1wm_Template::get_content( 'import/button-file' ) );
}
add_filter( 'ai1wm_import_buttons', 'cmp_remove_import_bottons',9999 );

function cmp_remove_export_bottons(){
	return array( Ai1wm_Template::get_content( 'export/button-file' ) );
}
add_filter( 'ai1wm_export_buttons', 'cmp_remove_export_bottons',9999 );

function max_file_size() {
	return 0;
}
add_filter( 'ai1wm_max_file_size', 'max_file_size', 9999 );

function cmp_ai1wm_customs_css(){
	echo '
	<style>
	.ai1wm-button-group.ai1wm-button-import.ai1wm-open>.ai1wm-dropdown-menu,
	.ai1wm-button-group.ai1wm-button-export.ai1wm-open>.ai1wm-dropdown-menu{
		height: 28px!important;
	}
	</style>
	';
}

add_action( 'admin_enqueue_scripts', 'cmp_ai1wm_customs_css', 9999 );

//end cmhello
