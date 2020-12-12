<?php
add_action('init',	['WPJAM_Route', 'init']);
add_action('init',	['WPJAM_Post_Type', 'init']);
add_action('init',	['WPJAM_Taxonomy', 'init']);

add_action('registered_taxonomy',	['WPJAM_Taxonomy', 'on_registered_taxonomy'], 1, 3);
add_action('registered_post_type',	['WPJAM_Post_Type', 'on_registered_post_type'], 1, 2);

add_action('pre_get_posts', 			['WPJAM_Post_Type', 'on_pre_get_posts'], 1);

add_filter('post_type_link', 			['WPJAM_Post_Type', 'filter_post_type_link'], 1, 2);
add_filter('posts_clauses',				['WPJAM_Post_Type', 'filter_posts_clauses'], 1, 2);
add_filter('post_password_required',	['WPJAM_Post_Type', 'filter_post_password_required'], 1, 2);
add_filter('pre_term_link',				['WPJAM_Taxonomy', 'filter_pre_term_link'], 1, 2);

if(wpjam_is_json_request()){
	remove_filter('the_title', 'convert_chars');

	remove_action('init', 'wp_widgets_init', 1);
	remove_action('init', 'maybe_add_existing_user_to_blog');
	remove_action('init', 'check_theme_switched', 99);

	remove_action('plugins_loaded', '_wp_customize_include');
	
	remove_action('wp_loaded', '_custom_header_background_just_in_time');

	add_filter('determine_current_user',	['WPJAM_User', 'filter_current_user']);
	add_filter('wp_get_current_commenter',	['WPJAM_User', 'filter_current_commenter']);

	wpjam_register_api('token.grant',	['token'=>true,'quota'=>1000]);
	// wpjam_register_api('token.validate',['grant'=>true,'quota'=>10]);
}

// 加载各种扩展
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-basic.php';		// 基础设置
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-custom.php';		// 样式定制
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-cdn.php';			// CDN 处理
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-thumbnail.php';	// 缩略图处理
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-crons.php';		// 定时作业
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-verify-txts.php';	// 验证 TXT
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-hooks.php';		// 基本优化
include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-compat.php';		// 兼容代码 

if(is_admin()){
	include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-posts.php';
	include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-dashboard.php';
	include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-upgrader.php';
}

add_action('wpjam_loaded', 		['WPJAM_Extends', 'load_extends']);
add_action('plugins_loaded',	['WPJAM_Extends', 'load_template_extends'], 0);

	
	