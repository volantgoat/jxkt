<?php
/*
Name: 移动主题
URI: https://blog.wpjam.com/m/mobile-theme/
Description: 给当前站点设置移动设备设置上使用单独的主题。
Version: 1.0
*/

add_action('plugins_loaded', function(){
	if(wp_is_mobile()){
		if(wpjam_basic_get_setting('mobile_stylesheet')){
			add_filter('stylesheet', function($stylesheet){
				return wpjam_basic_get_setting('mobile_stylesheet');
			});
		}

		if(wpjam_basic_get_setting('mobile_template')){
			add_filter('template', function($template){
				return wpjam_basic_get_setting('mobile_template');
			});
		}
	}
}, 0);

if(is_admin()){
	wpjam_add_menu_page('mobile-theme', [
		'parent'		=> 'themes',
		'menu_title'	=> '移动主题',
		'function'		=> 'option',
		'option_name'	=> 'wpjam-basic'
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page){
		if($plugin_page != 'mobile-theme'){
			return;
		}

		add_filter('wpjam_basic_setting', function(){
			$themes		= wp_get_themes();
			$current	= wp_get_theme();

			$theme_options		= [];
			$theme_options[$current->get_stylesheet()]	= $current->get('Name');

			foreach($themes as $theme){
				$theme_options[$theme->get_stylesheet()]	= $theme->get('Name');
			}

			return [
				'fields'	=> ['mobile_stylesheet'=>['title'=>'选择移动主题',	'type'=>'select',	'options'=>$theme_options]],
				'summary'	=> '使用手机和平板访问网站的用户将看到以下选择的主题界面，而桌面用户依然看到 <strong>'.$current->get('Name').'</strong> 主题界面。'
			];
		});

		add_action('sanitize_option_wpjam-basic', function($value){
			$mobile_stylesheet = $value['mobile_stylesheet'] ?? '';

			if($mobile_stylesheet){
				$mobile_theme	= wp_get_theme($mobile_stylesheet);
				$value['mobile_template']	= $mobile_theme->get_template();
			}

			return $value;
		});

	});
}

