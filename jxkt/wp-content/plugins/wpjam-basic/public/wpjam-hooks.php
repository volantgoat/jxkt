<?php
add_action('init',				['WPJAM_Hook', 'init']);
add_action('wp_loaded',			['WPJAM_HOOK', 'on_loaded']);
add_action('template_redirect',	['WPJAM_Hook', 'on_template_redirect']);

add_filter('register_taxonomy_args',		['WPJAM_HOOK', 'filter_register_taxonomy_args'], 1, 2);
add_filter('register_post_type_args', 		['WPJAM_HOOK', 'filter_register_post_type_args'], 10, 2);
add_filter('pre_get_avatar_data', 			['WPJAM_HOOK', 'filter_pre_get_avatar_data'], 10, 2);

add_filter('wpjam_option_use_site_default',	['WPJAM_Hook', 'filter_option_use_site_default'], 10, 2);
add_filter('wp_update_attachment_metadata', ['WPJAM_Hook', 'filter_update_attachment_metadata']);	// 修正任意文件删除漏洞

// 优化文章摘要
if($excerpt_optimization = wpjam_basic_get_setting('excerpt_optimization')){ 
	remove_filter('get_the_excerpt', 'wp_trim_excerpt');

	if($excerpt_optimization != 2){
		add_filter('get_the_excerpt', ['WPJAM_Hook', 'filter_get_the_excerpt'], 10, 2);
	}
}

//前台不加载语言包
if(wpjam_basic_get_setting('locale') && !is_admin()){
	add_filter('locale',	['WPJAM_Hook', 'filter_locale']);
}

// 解决日志改变 post type 之后跳转错误的问题，
// WP 原始解决函数 'wp_old_slug_redirect' 和 'redirect_canonical'
if(wpjam_basic_get_setting('404_optimization')){ 
	add_filter('old_slug_redirect_post_id',	['WPJAM_Hook', 'filter_old_slug_redirect_post_id']);
}

//移除 WP_Head 无关紧要的代码
if(wpjam_basic_get_setting('remove_head_links')){
	remove_action( 'wp_head', 'wp_generator');					//删除 head 中的 WP 版本号
	foreach (['rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head'] as $action) {
		remove_action( $action, 'the_generator' );
	}

	remove_action( 'wp_head', 'rsd_link' );						//删除 head 中的 RSD LINK
	remove_action( 'wp_head', 'wlwmanifest_link' );				//删除 head 中的 Windows Live Writer 的适配器？ 

	remove_action( 'wp_head', 'feed_links_extra', 3 );		  	//删除 head 中的 Feed 相关的link
	//remove_action( 'wp_head', 'feed_links', 2 );	

	remove_action( 'wp_head', 'index_rel_link' );				//删除 head 中首页，上级，开始，相连的日志链接
	remove_action( 'wp_head', 'parent_post_rel_link', 10); 
	remove_action( 'wp_head', 'start_post_rel_link', 10); 
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10);

	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );	//删除 head 中的 shortlink
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10);	// 删除头部输出 WP RSET API 地址

	remove_action( 'template_redirect',	'wp_shortlink_header', 11);		//禁止短链接 Header 标签。	
	remove_action( 'template_redirect',	'rest_output_link_header', 11);	// 禁止输出 Header Link 标签。
}

//让用户自己决定是否书写正确的 WordPress
if(wpjam_basic_get_setting('remove_capital_P_dangit')){
	remove_filter( 'the_content', 'capital_P_dangit', 11 );
	remove_filter( 'the_title', 'capital_P_dangit', 11 );
	remove_filter( 'wp_title', 'capital_P_dangit', 11 );
	remove_filter( 'comment_text', 'capital_P_dangit', 31 );
}

// 屏蔽字符转码
if(wpjam_basic_get_setting('disable_texturize')){
	add_filter('run_wptexturize', '__return_false');
}

//移除 admin bar
if(wpjam_basic_get_setting('remove_admin_bar')){
	add_filter('show_admin_bar', '__return_false');
}

//禁用 XML-RPC 接口
if(wpjam_basic_get_setting('disable_xml_rpc')){
	if(wpjam_basic_get_setting('disable_block_editor')){
		add_filter( 'xmlrpc_enabled', '__return_false' );
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
	}
}

// 屏蔽古腾堡编辑器
if(wpjam_basic_get_setting('disable_block_editor')){
	remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
	remove_action('admin_enqueue_scripts', 'wp_common_block_scripts_and_styles');
	remove_filter('the_content', 'do_blocks', 9);
}

// 屏蔽站点管理员邮箱验证功能
if(wpjam_basic_get_setting('disable_admin_email_check')){
	add_filter('admin_email_check_interval', '__return_false');
}

// 屏蔽 Emoji
if(wpjam_basic_get_setting('disable_emoji')){  
	remove_action('admin_print_scripts','print_emoji_detection_script');
	remove_action('admin_print_styles',	'print_emoji_styles');

	remove_action('wp_head',			'print_emoji_detection_script',	7);
	remove_action('wp_print_styles',	'print_emoji_styles');

	remove_action('embed_head',			'print_emoji_detection_script');

	remove_filter('the_content_feed',	'wp_staticize_emoji');
	remove_filter('comment_text_rss',	'wp_staticize_emoji');
	remove_filter('wp_mail',			'wp_staticize_emoji_for_email');

	add_filter('emoji_svg_url',		'__return_false');
	add_filter('tiny_mce_plugins',	function($plugins){ 
		return array_diff($plugins, ['wpemoji']); 
	});
}

//禁用文章修订功能
if(wpjam_basic_get_setting('disable_revision')){
	if(!defined('WP_POST_REVISIONS')){
		define('WP_POST_REVISIONS', false);
	}
	
	remove_action('pre_post_update', 'wp_save_post_revision');
}

// 屏蔽Trackbacks
if(wpjam_basic_get_setting('disable_trackbacks')){
	if(wpjam_basic_get_setting('disable_xml_rpc')){
		//彻底关闭 pingback
		add_filter('xmlrpc_methods',function($methods){
			return array_merge($methods, [
				'pingback.ping'						=> '__return_false',
				'pingback.extensions.getPingbacks'	=> '__return_false'
			]);
		});
	}

	//禁用 pingbacks, enclosures, trackbacks 
	remove_action( 'do_pings', 'do_all_pings', 10 );

	//去掉 _encloseme 和 do_ping 操作。
	remove_action( 'publish_post','_publish_post_hook',5 );
}

// 屏蔽 REST API
if(wpjam_basic_get_setting('disable_rest_api')){
	remove_action('init',			'rest_api_init' );
	remove_action('rest_api_init',	'rest_api_default_filters', 10 );
	remove_action('parse_request',	'rest_api_loaded' );

	add_filter('rest_enabled',		'__return_false');
	// add_filter('rest_jsonp_enabled','__return_false');

	// 移除头部 wp-json 标签和 HTTP header 中的 link 
	remove_action('wp_head',			'rest_output_link_wp_head', 10 );
	remove_action('template_redirect',	'rest_output_link_header', 11);

	remove_action('xmlrpc_rsd_apis',	'rest_output_rsd');

	remove_action('auth_cookie_malformed',		'rest_cookie_collect_status');
	remove_action('auth_cookie_expired',		'rest_cookie_collect_status');
	remove_action('auth_cookie_bad_username',	'rest_cookie_collect_status');
	remove_action('auth_cookie_bad_hash',		'rest_cookie_collect_status');
	remove_action('auth_cookie_valid',			'rest_cookie_collect_status');
	remove_filter('rest_authentication_errors',	'rest_cookie_check_errors', 100 );
}

//禁用 Auto OEmbed
if(wpjam_basic_get_setting('disable_autoembed')){ 
	remove_filter('the_content',			[$GLOBALS['wp_embed'], 'run_shortcode'], 8);
	remove_filter('widget_text_content',	[$GLOBALS['wp_embed'], 'run_shortcode'], 8);

	remove_filter('the_content',			[$GLOBALS['wp_embed'], 'autoembed'], 8);
	remove_filter('widget_text_content',	[$GLOBALS['wp_embed'], 'autoembed'], 8);

	remove_action('edit_form_advanced',		[$GLOBALS['wp_embed'], 'maybe_run_ajax_cache']);
	remove_action('edit_page_form',			[$GLOBALS['wp_embed'], 'maybe_run_ajax_cache']);
}

// 屏蔽文章Embed
if(wpjam_basic_get_setting('disable_post_embed')){  
	
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );

	add_filter( 'embed_oembed_discover', '__return_false' );

	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	remove_filter( 'oembed_response_data',   'get_oembed_response_data_rich',  10, 4 );

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );

	add_filter('tiny_mce_plugins', function ($plugins){
		return array_diff( $plugins, ['wpembed'] );
	});
}

// 屏蔽站点Feed
if(wpjam_basic_get_setting('disable_feed')){
	add_action('do_feed',		['WPJAM_Hook', 'feed_disabled'], 1);
	add_action('do_feed_rdf',	['WPJAM_Hook', 'feed_disabled'], 1);
	add_action('do_feed_rss',	['WPJAM_Hook', 'feed_disabled'], 1);
	add_action('do_feed_rss2',	['WPJAM_Hook', 'feed_disabled'], 1);
	add_action('do_feed_atom',	['WPJAM_Hook', 'feed_disabled'], 1);
}

// 屏蔽自动更新
if(wpjam_basic_get_setting('disable_auto_update')){  
	add_filter('automatic_updater_disabled', '__return_true');
	remove_action('init', 'wp_schedule_update_checks');
}

// 禁止使用 admin 用户名尝试登录
if(wpjam_basic_get_setting('no_admin')){
	add_filter( 'wp_authenticate',  function ($user){
		if($user == 'admin') exit;
	});

	add_filter('sanitize_user', function ($username, $raw_username, $strict){
		if($raw_username == 'admin' || $username == 'admin'){
			exit;
		}
		return $username;
	}, 10, 3);
}

if(wpjam_basic_get_setting('x-frame-options')){
	add_action('send_headers', function ($wp){
		header('X-Frame-Options: '.wpjam_basic_get_setting('x-frame-options'));
	});
}

// 屏蔽后台隐私
if(wpjam_basic_get_setting('disable_privacy')){
	remove_action( 'user_request_action_confirmed', '_wp_privacy_account_request_confirmed' );
	remove_action( 'user_request_action_confirmed', '_wp_privacy_send_request_confirmation_notification', 12 ); // After request marked as completed.
	remove_action( 'wp_privacy_personal_data_exporters', 'wp_register_comment_personal_data_exporter' );
	remove_action( 'wp_privacy_personal_data_exporters', 'wp_register_media_personal_data_exporter' );
	remove_action( 'wp_privacy_personal_data_exporters', 'wp_register_user_personal_data_exporter', 1 );
	remove_action( 'wp_privacy_personal_data_erasers', 'wp_register_comment_personal_data_eraser' );
	remove_action( 'init', 'wp_schedule_delete_old_privacy_export_files' );
	remove_action( 'wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files' );

	add_filter('option_wp_page_for_privacy_policy', '__return_zero');
}

if(is_admin()){
	add_action('wpjam_admin_init',				['WPJAM_Basic', 'add_menu_pages']);
	add_filter('default_option_wpjam-basic',	['WPJAM_Basic', 'get_default_settings']);

	wpjam_register_page_action('delete_notice', [
		'tag'			=> 'span',
		'class'			=> 'hidden',
		'button_text'	=> '删除',
		'direct'		=> true,
		'callback'		=> ['WPJAM_Notice', 'ajax_delete']
	]);

	add_action('admin_notices',				['WPJAM_Notice', 'on_admin_notices']);
	add_action('admin_page_access_denied',	['WPJAM_Hook', 'on_admin_page_access_denied']);

	add_filter('admin_title', ['WPJAM_Hook','filter_admin_title']);

	remove_action('admin_init', 'zh_cn_l10n_legacy_option_cleanup');
	remove_action('admin_init', 'zh_cn_l10n_settings_init');

	// add_filter('is_protected_meta', function($protected, $meta_key){
	// 	return $protected ?: in_array($meta_key, ['views', 'favs']);
	// }, 10, 2);

	// add_filter('removable_query_args', function($removable_query_args){
	// 	return array_merge($removable_query_args, ['added', 'duplicated', 'unapproved',	'unpublished', 'published', 'geted', 'created', 'synced']);
	// });

	if(wpjam_basic_get_setting('disable_auto_update')){
		remove_action('admin_init', '_maybe_update_core');
		remove_action('admin_init', '_maybe_update_plugins');
		remove_action('admin_init', '_maybe_update_themes');
	}

	if(wpjam_basic_get_setting('remove_help_tabs')){  
		add_action('in_admin_header', function(){
			$GLOBALS['current_screen']->remove_help_tabs();
		});
	}

	if(wpjam_basic_get_setting('remove_screen_options')){  
		add_filter('screen_options_show_screen', '__return_false');
		add_filter('hidden_columns', '__return_empty_array');
	}

	if(wpjam_basic_get_setting('disable_privacy')){
		add_action('admin_menu', function(){
			remove_submenu_page('options-general.php', 'options-privacy.php');
			remove_submenu_page('tools.php', 'export-personal-data.php');
			remove_submenu_page('tools.php', 'erase-personal-data.php');
		},11);

		add_action('admin_init', function(){
			remove_action('admin_init', ['WP_Privacy_Policy_Content', 'text_change_check'], 100);
			remove_action('edit_form_after_title', ['WP_Privacy_Policy_Content', 'notice']);
			remove_action('admin_init', ['WP_Privacy_Policy_Content', 'add_suggested_content'], 1);
			remove_action('post_updated', ['WP_Privacy_Policy_Content', '_policy_page_updated']);
			remove_filter('list_pages', '_wp_privacy_settings_filter_draft_page_titles', 10, 2);
		},1);
	}

	// 防止重名造成大量的 SQL 请求
	if(wpjam_basic_get_setting('timestamp_file_name')){
		add_filter('wp_handle_sideload_prefilter',	['WPJAM_Hook', 'timestamp_file_name']);
		add_filter('wp_handle_upload_prefilter',	['WPJAM_Hook', 'timestamp_file_name']);
	}
}