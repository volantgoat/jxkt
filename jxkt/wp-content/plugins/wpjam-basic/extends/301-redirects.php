<?php
/*
Name: 301 跳转
URI: https://blog.wpjam.com/m/301-redirects/
Description: 支持设置网站上的 404 页面跳转到正确页面。
Version: 1.0
*/
add_action('template_redirect',function(){
	if(!is_404()) {
		return;
	}

	$request_url =  wpjam_get_current_page_url();

	if(strpos($request_url, 'feed/atom/') !== false){
		wp_redirect(str_replace('feed/atom/', '', $request_url), 301);
		exit;
	}

	if(strpos($request_url, 'comment-page-') !== false){
		wp_redirect(preg_replace('/comment-page-(.*)\//', '',  $request_url), 301);
		exit;
	}

	if(strpos($request_url, 'page/') !== false){
		wp_redirect(preg_replace('/page\/(.*)\//', '',  $request_url), 301);
		exit;
	}

	if($wpjam_301_redirects = get_option('301-redirects')){
		foreach ($wpjam_301_redirects as $wpjam_301_redirect) {
			if($wpjam_301_redirect['request'] == $request_url){
				wp_redirect($wpjam_301_redirect['destination'], 301);
				exit;
			}
		}
	}
}, 99);

if(is_admin()){
	wpjam_add_basic_sub_page('301-redirects', [
		'menu_title'	=> '301跳转',
		'function'		=> 'list',
		'summary'		=> '301跳转扩展让一些404页面正确跳转到正常页面，详细介绍请点击：<a href="https://blog.wpjam.com/m/301-redirects/" target="_blank">301 跳转扩展</a>。'
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page){
		if($plugin_page != '301-redirects'){
			return;
		}

		wpjam_register_list_table('301-redirects', [
			'title'		=> '301跳转',
			'plural'	=> 'redirects',
			'singular' 	=> 'redirect',
			'model'		=> 'WPJAM_Admin301Redirect',
			'fixed'		=> false,
			'ajax'		=> true,
			'per_page'	=> 50
		]);

		class WPJAM_Admin301Redirect extends WPJAM_Model {
			private static $handler;

			public static function get_handler(){
				if(is_null(static::$handler)){
					static::$handler	= new WPJAM_Option('301-redirects', ['total'=>50, 'primary_key'=>'id']);
				}
				return static::$handler;
			}

			public static function get_fields($action_key='', $id=0){
				return [
					'request'		=> ['title'=>'原地址',	'type'=>'url',	'show_admin_column'=>true],
					'destination'	=> ['title'=>'目标地址',	'type'=>'url',	'show_admin_column'=>true]
				];
			}
		}
	});
}