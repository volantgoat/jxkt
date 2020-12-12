<?php
class WPJAM_Custom{
	use WPJAM_Setting_Trait;

	private function __construct(){
		$this->init('wpjam-custom');
	}

	public function get_setting($name){
		if(!is_null($this->settings)){
			return $this->settings[$name] ?? '';
		}else{
			return wpjam_basic_get_setting($name);
		}
	}

	public static function get_option_setting($section=''){
		return [
			'wpjam-custom'	=> ['title'=>'前台定制',	'fields'=>[
				'head'			=> ['title'=>'前台 Head 代码',		'type'=>'textarea',	'class'=>''],
				'footer'		=> ['title'=>'前台 Footer 代码',		'type'=>'textarea',	'class'=>''],
			]],
			'admin-custom'	=> ['title'=>'后台定制',	'fields'=>[
				'admin_logo'	=> ['title'=>'后台左上角 Logo',		'type'=>'img',	'item_type'=>'url',	'description'=>'建议大小：20x20。'],
				'admin_head'	=> ['title'=>'后台 Head 代码 ',		'type'=>'textarea',	'class'=>''],
				'admin_footer'	=> ['title'=>'后台 Footer 代码',		'type'=>'textarea',	'class'=>'']
			]],
			'login-custom'	=> ['title'=>'登录界面', 	'fields'=>[
				// 'login_logo'			=> ['title'=>'登录界面 Logo',		'type'=>'img',		'description'=>'建议大小：宽度不超过600px，高度不超过160px。'),
				'login_head'	=> ['title'=>'登录界面 Head 代码',	'type'=>'textarea',	'class'=>''],
				'login_footer'	=> ['title'=>'登录界面 Footer 代码',	'type'=>'textarea',	'class'=>''],
				'login_redirect'=> ['title'=>'登录之后跳转的页面',		'type'=>'text'],
			]]
		];
	}

	public static function on_admin_bar_menu($wp_admin_bar){
		$admin_logo	= self::get_instance()->get_setting('admin_logo');
		$title 		= $admin_logo ? '<img src="'.wpjam_get_thumbnail($admin_logo, 40, 40).'" style="height:20px; padding:6px 0">' : '<span class="ab-icon"></span>';

		$wp_admin_bar->add_menu([
			'id'    => 'wp-logo',
			'title' => $title,
			'href'  => self_admin_url(),
			'meta'  => ['title'=>get_bloginfo('name')]
		]);
	}

	public static function filter_admin_footer_text($text){
		return self::get_instance()->get_setting('admin_footer') ?: $text;
	}

	public static function on_login_head(){
		echo self::get_instance()->get_setting('login_head'); 
	}

	public static function on_login_footer(){
		echo self::get_instance()->get_setting('login_footer'); 
	}

	public static function filter_login_redirect($redirect_to, $request){
		return $request ?: (self::get_instance()->get_setting('login_redirect') ?: $redirect_to);
	}

	public static function on_wp_head(){
		echo self::get_instance()->get_setting('head'); 
	}

	public static function on_wp_footer(){
		echo self::get_instance()->get_setting('footer');

		if(wpjam_basic_get_setting('optimized_by_wpjam')){
			echo '<p id="optimized_by_wpjam_basic">Optimized by <a href="https://blog.wpjam.com/project/wpjam-basic/">WPJAM Basic</a>。</p>';
		}
	}
}

if(is_admin()){
	wpjam_add_basic_sub_page('wpjam-custom', [
		'menu_title'	=> '样式定制',		
		'function'		=> 'option',
		'order'			=> 20,
		'summary'		=> '对网站的前端或者后台的样式进行定制，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-custom-setting/"  target="_blank">样式定制</a>。',
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page){
		if($plugin_page == 'wpjam-custom'){
			wpjam_register_option('wpjam-custom', ['WPJAM_Custom','get_option_setting']);
		}
	});

	add_action('admin_head', function(){
		remove_action('admin_bar_menu',	'wp_admin_bar_wp_menu', 10);
		
		add_action('admin_bar_menu',	['WPJAM_Custom', 'on_admin_bar_menu']);

		echo WPJAM_Custom::get_instance()->get_setting('admin_head');
	});

	add_filter('admin_footer_text', ['WPJAM_Custom', 'filter_admin_footer_text']);
}elseif(is_login()){
	add_filter('login_headerurl',	'home_url');
	add_filter('login_headertext',	'get_bloginfo');

	add_action('login_head', 		['WPJAM_Custom', 'on_login_head']);
	add_action('login_footer',		['WPJAM_Custom', 'on_login_footer']);
	add_filter('login_redirect',	['WPJAM_Custom', 'filter_login_redirect'], 10, 2);
}else{
	add_action('wp_head',	['WPJAM_Custom', 'on_wp_head'], 1);
	add_action('wp_footer', ['WPJAM_Custom', 'on_wp_footer'], 99);
}