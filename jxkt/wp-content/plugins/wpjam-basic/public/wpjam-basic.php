<?php
class WPJAM_Basic{
	use WPJAM_Setting_Trait;

	private function __construct(){
		$this->init('wpjam-basic');
	}

	public function get_setting($name){
		$value	= $this->settings[$name] ?? null;

		if($value){
			if($name == 'disable_rest_api'){
				return !empty($settings['disable_post_embed']) && !empty($settings['disable_block_editor']);
			}elseif($name == 'disable_xml_rpc'){
				return !empty($settings['disable_block_editor']);
			}
		}

		return $value;
	}

	public static function get_default_settings(){
		return [
			'disable_revision'			=> 1,
			'disable_trackbacks'		=> 1,
			'disable_emoji'				=> 1,
			'disable_texturize'			=> 1,
			'disable_privacy'			=> 1,
			
			'remove_head_links'			=> 1,
			'remove_capital_P_dangit'	=> 1,

			'admin_footer'				=> '<span id="footer-thankyou">感谢使用<a href="https://cn.wordpress.org/" target="_blank">WordPress</a>进行创作。</span> | <a href="http://wpjam.com/" title="WordPress JAM" target="_blank">WordPress JAM</a>'
		];
	}

	private static $sub_pages	= [];

	public static function add_sub_page($sub_slug, $args=[]){
		self::$sub_pages[$sub_slug]	= $args;
	}

	public static function add_menu_pages(){
		$subs	= [];

		$subs['wpjam-basic']	= [
			'menu_title'	=> '优化设置',	
			'function'		=> 'option',
			'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-basic.php',
			'summary'		=> '优化设置让你通过关闭一些不常用的功能来加快  WordPress 的加载。
		但是某些功能的关闭可能会引起一些操作无法执行，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-optimization-setting/" target="_blank">优化设置</a>。'
		];
		
		$verified	= WPJAM_Verify::verify();

		if(!$verified){
			$subs['wpjam-verify']	= [
				'menu_title'	=> '扩展管理',
				'page_title'	=> '验证 WPJAM',
				'function'		=> 'form',
				'form_name'		=> 'verify_wpjam',
				'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-verify.php'
			];
		}else{
			$subs	+= self::$sub_pages;
			$subs	= apply_filters('wpjam_basic_sub_pages', $subs);

			$subs['server-status']	= [
				'menu_title'	=> '系统信息',		
				'function'		=> 'tab',
				'capability'	=> is_multisite() ? 'manage_sites' : 'manage_options',	
				'page_file'		=> WPJAM_BASIC_PLUGIN_DIR .'admin/pages/server-status.php',
				'summary'		=> '系统信息扩展让你在后台就能够快速实时查看当前的系统状态，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-service-status/" target="_blank">系统信息扩展</a>。'
			];

			if(!is_multisite() || !is_network_admin()){
				$subs['dashicons']		= [
					'menu_title'	=> 'Dashicons',
					'page_file'		=> WPJAM_BASIC_PLUGIN_DIR .'admin/pages/dashicons.php',	
					'summary'		=> 'Dashicons 功能列出所有的 Dashicons 以及每个 Dashicon 的名称和 HTML 代码，详细介绍请查看：<a href="https://blog.wpjam.com/m/wpjam-basic-dashicons/" target="_blank">Dashicons</a>，在 WordPress 后台<a href="https://blog.wpjam.com/m/using-dashicons-in-wordpress-admin/" target="_blank">如何使用 Dashicons</a>。'
				];
			}

			$subs['wpjam-extends']	= [
				'menu_title'	=> '扩展管理',
				'function'		=> 'option',
				'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-extends.php'
			];

			if($verified !== 'verified'){
				$subs['wpjam-basic-topics']	= [
					'menu_title'	=> '讨论组',
					'function'		=> 'tab',
					'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-topics.php',
					'tabs'			=> [
						'topics'	=> ['title'=>'讨论组',	'function'=>'list'],
						'message'	=> ['title'=>'消息提醒',	'function'=>'wpjam_topic_user_messages_page'],
						'profile'	=> ['title'=>'个人资料',	'function'=>'form',	'form_name'=>'delete_weixin_user'],
					]
				];
			}
		}

		if($verified !== 'verified'){
			$subs['wpjam-about']	= [
				'menu_title'	=> '关于WPJAM',	
				'function'		=> 'wpjam_basic_about_page',	
				'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-about.php'
			];
		}

		$basic_menu	= [
			'menu_title'	=> 'WPJAM',	
			'icon'			=> 'dashicons-performance',
			'position'		=> '58.99',	
			'function'		=> 'option',	
			'subs'			=> $subs
		];

		if(is_multisite() && is_network_admin()){
			$basic_menu['network']	= true;
		}

		wpjam_add_menu_page('wpjam-basic', $basic_menu);

		wpjam_add_menu_page('wpjam-messages', [
			'parent'		=> 'users',
			'menu_title'	=> '站内消息',
			'capability'	=> 'read',
			'page_file'		=> WPJAM_BASIC_PLUGIN_DIR.'admin/pages/wpjam-messages.php'
		]);

		add_action('admin_menu', function(){
			$GLOBALS['menu']['58.88']	= ['',	'read',	'separator'.'58.88', '', 'wp-menu-separator'];
		});
	}
}

class WPJAM_Hook{
	public static function get_setting($name){
		return wpjam_basic_get_setting($name);
	}

	public static function init(){
		if(	//阻止非法访问
			// strlen($_SERVER['REQUEST_URI']) > 255 ||
			strpos($_SERVER['REQUEST_URI'], "eval(") ||
			strpos($_SERVER['REQUEST_URI'], "base64") ||
			strpos($_SERVER['REQUEST_URI'], "/**/")
		){
			@header("HTTP/1.1 414 Request-URI Too Long");
			@header("Status: 414 Request-URI Too Long");
			@header("Connection: Close");
			exit;
		}

		if(self::get_setting('disable_trackbacks')){
			$GLOBALS['wp']->remove_query_var('tb');
		}

		if(self::get_setting('disable_post_embed')){ 
			$GLOBALS['wp']->remove_query_var('embed');
		}

		// 去掉URL中category
		if(wpjam_basic_get_setting('no_category_base') && !$GLOBALS['wp_rewrite']->use_verbose_page_rules){
			add_filter('request',		['WPJAM_Hook', 'filter_request']);
			add_filter('pre_term_link',	['WPJAM_Hook', 'filter_pre_term_link'], 1, 2);
		}

		// if(self::get_setting('disable_feed')){
		// 	$wp->remove_query_var('feed');
		// 	$wp->remove_query_var('withcomments');
		// 	$wp->remove_query_var('withoutcomments');
		// }
		
		wp_embed_unregister_handler('tudou');
		wp_embed_unregister_handler('youku');
		wp_embed_unregister_handler('56com');
	}

	public static function on_loaded(){
		ob_start(['WPJAM_Hook', 'html_replace']);
	}

	public static function html_replace($html){
		// Google字体加速
		if(self::get_setting('google_fonts')){
			$google_font_searchs	= [
				'googleapis_fonts'			=> '//fonts.googleapis.com', 
				'googleapis_ajax'			=> '//ajax.googleapis.com',
				'googleusercontent_themes'	=> '//themes.googleusercontent.com',
				'gstatic_fonts'				=> '//fonts.gstatic.com',
			];

			$search	= $replace = [];

			if(self::get_setting('google_fonts') == 'custom'){
				foreach ($google_font_searchs as $google_font_key => $google_font_search) {
					if(self::get_setting($google_font_key)){
						$search[]	= $google_font_search;
						$replace[]	= str_replace(['http://','https://'], '//', $google_font_search);
					}
				}
			}elseif(self::get_setting('google_fonts') == 'ustc'){
				$search		= array_values($google_font_searchs);
				$replace	= [
					'//fonts.lug.ustc.edu.cn',
					'//ajax.lug.ustc.edu.cn',
					'//google-themes.lug.ustc.edu.cn',
					'//fonts-gstatic.lug.ustc.edu.cn',
				];
			}

			$html	= $search ? str_replace($search, $replace, $html) : $html;
		}

		return apply_filters('wpjam_html', $html);
	}

	public static function feed_disabled() {
		wp_die('Feed已经关闭, 请访问<a href="'.get_bloginfo('url').'">网站首页</a>！');
	}

	public static function on_admin_page_access_denied(){
		if((is_multisite() && is_user_member_of_blog(get_current_user_id(), get_current_blog_id())) || !is_multisite()){
			wp_die(__( 'Sorry, you are not allowed to access this page.' ).'<a href="'.admin_url().'">返回首页</a>', 403);
		}
	}

	public static function timestamp_file_name($file){
		return array_merge($file, ['name'=> time().'-'.$file['name']]);
	}

	public static function filter_admin_title($admin_title){
		return str_replace(' &#8212; WordPress', '', $admin_title);
	}

	public static function filter_option_use_site_default($status, $option_name){
		if(in_array($option_name, ['wpjam-basic', 'wpjam-custom', 'wpjam-cdn', 'wpjam-thumbnail', 'wpjam-extends'])){
			return true;
		}

		return $status;
	}

	public static function filter_update_attachment_metadata($data){
		if(isset($data['thumb'])){
			$data['thumb'] = basename($data['thumb']);
		}

		return $data;
	}

	public static function filter_register_post_type_args($args, $post_type){
		if(self::get_setting('disable_rest_api')){	// 屏蔽 REST API
			$args['show_in_rest']	= false;
		}

		if(!empty($args['supports']) && is_array($args['supports'])){
			if(self::get_setting('disable_trackbacks')){	// 屏蔽 Trackback
				$args['supports']	= array_diff($args['supports'], ['trackbacks']);

				remove_post_type_support($post_type, 'trackbacks');	// create_initial_post_types 会执行两次
			}

			if(self::get_setting('disable_revision')){	//禁用日志修订功能
				$args['supports']	= array_diff($args['supports'], ['revisions']);

				remove_post_type_support($post_type, 'revisions');
			}
		}

		return $args;
	}

	public static function filter_register_taxonomy_args($args,  $taxonomy){
		if(self::get_setting('disable_rest_api')){	// 屏蔽 REST API
			$args['show_in_rest']	= false;
		}

		$args['supports']	= $args['supports'] ?? ['slug', 'description', 'parent'];
		$args['levels']		= $args['levels'] ?? null;

		return $args;
	}

	public static function filter_pre_term_link($term_link, $term){
		$no_base_taxonomy	= self::get_setting('no_category_base_for') ?: 'category';
			
		if($term->taxonomy == $no_base_taxonomy){
			if($term->taxonomy == 'category'){
				return '%category%';
			}else{
				return "%$taxonomy%";
			}
		}

		return $term_link;
	}

	public static function filter_request($query_vars) {
		if(!isset($query_vars['module']) && !isset($_GET['page_id']) && !isset($_GET['pagename']) && !empty($query_vars['pagename'])){
			$pagename	= strtolower($query_vars['pagename']);
			$pagename	= wp_basename($pagename);
			
			$taxonomy	= self::get_setting('no_category_base_for') ?: 'category';
			$terms		= get_categories(['taxonomy'=>$taxonomy,'hide_empty'=>false]);
			$terms		= wp_list_pluck($terms, 'slug');

			if(in_array($pagename, $terms)){
				unset($query_vars['pagename']);
				if($taxonomy == 'category'){
					$query_vars['category_name']	= $pagename;
				}else{
					$query_vars['taxonomy']	= $taxonomy;
					$query_vars['term']		= $pagename;
				}
			}
		}

		return $query_vars;
	}

	public static function on_template_redirect(){
		if(self::get_setting('no_category_base')){
			$taxonomy	= self::get_setting('no_category_base_for') ?: 'category';

			if(strpos($_SERVER['REQUEST_URI'], '/'.$taxonomy.'/') === false){
				return;
			}

			if((is_category() && $taxonomy == 'category') || is_tax($taxonomy)){
				wp_redirect(site_url(str_replace('/'.$taxonomy, '', $_SERVER['REQUEST_URI'])), 301);
				exit;
			}
		}

		//搜索关键词为空时直接重定向到首页
		//当搜索结果只有一篇时直接重定向到文章
		if(self::get_setting('search_optimization')){
			if(is_search() && get_query_var('module') == '') {
				global $wp_query;

				if(empty($wp_query->query['s'])){
					wp_redirect(home_url());
				}else{
					$paged	= get_query_var('paged');
					if ($wp_query->post_count == 1 && empty($paged)) {
						wp_redirect(get_permalink($wp_query->posts['0']->ID));
					}
				}
			}
		}			
	}

	public static function filter_old_slug_redirect_post_id($post_id){
		if($post_id){
			return $post_id;
		}

		global $wpdb;

		$post_ids	= $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_old_slug' AND meta_value = %s", get_query_var('name')));

		if(empty($post_ids)){
			return null;
		}

		$posts	= array_filter(WPJAM_Post::get_by_ids($post_ids), function($post){
			return $post->post_status == 'publish';
		});

		if(empty($posts)){
			return null;
		}

		$post_id	= current($posts)->ID;
		$post_type	= get_query_var('post_type');

		if(count($posts) > 1 && $post_type && !is_null($post_type) && $post_type != 'any'){ // 指定 post_type 则获取首先获取 post_type 相同的
			$filtered_posts	= array_filter($posts, function($post) use($post_type){
				return $post->post_type == $post_type;
			});

			if($filtered_posts){
				$post_id	= current($filtered_posts)->ID;
			}
		}

		return $post_id;
	}

	private static $locale = null;

	public static function filter_locale($locale){
		if(is_null(self::$locale)){
			self::$locale	= $locale;	
		}

		if(in_array('get_language_attributes', wp_list_pluck(debug_backtrace(), 'function'))){
			return self::$locale;
		}else{
			return 'en_US';
		}
	}

	public static function filter_pre_get_avatar_data($args, $id_or_email){
		$email_hash	= '';
		$user		= $email = false;
		
		if(is_object($id_or_email) && isset($id_or_email->comment_ID)){
			$id_or_email	= get_comment($id_or_email);
		}

		if(is_numeric($id_or_email)){
			$user	= get_user_by('id', absint($id_or_email));
		}elseif($id_or_email instanceof WP_User){	// User Object
			$user	= $id_or_email;
		}elseif($id_or_email instanceof WP_Post){	// Post Object
			$user	= get_user_by('id', intval($id_or_email->post_author));
		}elseif($id_or_email instanceof WP_Comment){	// Comment Object
			$avatar = get_comment_meta($id_or_email->comment_ID, 'avatarurl', true);

			if($avatar){
				$args['url']	= wpjam_get_thumbnail($avatar, [$args['width'],$args['height']]);
				$args['found_avatar']	= true;

				return $args;
			}

			if(!empty($id_or_email->user_id)){
				$user	= get_user_by('id', intval($id_or_email->user_id));
			}elseif(!empty($id_or_email->comment_author_email)){
				$email	= $id_or_email->comment_author_email;
			}
		}elseif(is_string($id_or_email)){
			if(strpos($id_or_email, '@md5.gravatar.com')){
				list($email_hash)	= explode('@', $id_or_email);
			} else {
				$email	= $id_or_email;
			}
		}

		if($user){
			$avatar = get_user_meta($user->ID, 'avatarurl', true);

			if($avatar){
				$args['url']	= wpjam_get_thumbnail(set_url_scheme($avatar), [$args['width'],$args['height']]);
				$args['found_avatar']	= true;

				return $args;
			}else{
				$args	= apply_filters('wpjam_default_avatar_data', $args, $user->ID);

				if($args['found_avatar']){
					return $args;
				}else{
					$email = $user->user_email;
				}
			}
		}

		if(!$email_hash && $email){
			$email_hash = md5(strtolower(trim($email)));
		}

		if($email_hash){
			$args['found_avatar']	= true;

			$gravatar_url	= 'http://cn.gravatar.com/avatar/';

			// Gravatar加速
			if($gravatar_setting = self::get_setting('gravatar')){
				if($gravatar_setting == 'custom'){
					if($gravatar_custom	= self::get_setting('gravatar_custom')){
						$gravatar_url	= $gravatar_custom;
					}
				}elseif($gravatar_setting == 'v2ex'){
					$gravatar_url	= 'http://cdn.v2ex.com/gravatar/';
				}
			}

			$url	= $gravatar_url.$email_hash;
			$url_args	= array_filter([
				's'	=> $args['size'],
				'd'	=> $args['default'],
				'f'	=> $args['force_default'] ? 'y' : false,
				'r'	=> $args['rating'],
			]);

			$url	= add_query_arg(rawurlencode_deep($url_args), set_url_scheme($url, $args['scheme']));

			$args['url']	= apply_filters('get_avatar_url', $url, $id_or_email, $args);
		}

		return $args;
	}

	public static function filter_get_the_excerpt($text='', $post=null){
		if(empty($text)){
			remove_filter('the_excerpt', 'wp_filter_content_tags');

			$length	= self::get_setting('excerpt_length') ?: 200;	
			$text	= wpjam_get_post_excerpt($post, $length);
		}

		return $text;
	}
}

class WPJAM_Extends{
	use WPJAM_Setting_trait;

	protected function __construct(){
		$this->init('wpjam-extends');

		$this->settings	= $this->settings ? array_filter($this->settings) : [];

		if(is_multisite()){
			$sitewide_extendss	= get_site_option('wpjam-extends');
			$sitewide_extendss	= $sitewide_extendss ? array_filter($sitewide_extendss) : [];
			
			if($sitewide_extendss){
				$this->settings	= array_merge($this->settings, $sitewide_extendss);
			}
		}
	}

	public function has($extend){
		$extend	= rtrim($extend, '.php').'.php';

		return $this->get_setting($extend) ? true : false;
	}

	public static function load_extends(){
		$instance	= self::get_instance();
		if($extends = $instance->get_settings()){
			foreach (array_keys($extends) as $extend_file) {
				if(is_file(WPJAM_BASIC_PLUGIN_DIR.'extends/'.$extend_file)){
					include WPJAM_BASIC_PLUGIN_DIR.'extends/'.$extend_file;
				}
			}
		}
	}

	public static function load_template_extends(){
		$template_extend_dir	= get_template_directory().'/extends';

		if(is_dir($template_extend_dir)){
			if($extend_handle = opendir($template_extend_dir)) {   
				while (($extend = readdir($extend_handle)) !== false) {
					if ($extend == '.' || $extend == '..' || is_file($template_extend_dir.'/'.$extend)) {
						continue;
					}
					
					if(is_file($template_extend_dir.'/'.$extend.'/'.$extend.'.php')){
						include $template_extend_dir.'/'.$extend.'/'.$extend.'.php';
					}
				}   
				closedir($extend_handle);   
			}
		}
	}
}

function wpjam_basic_get_setting($name){
	$instance	= WPJAM_Basic::get_instance();

	return $instance->get_setting($name);
}

function wpjam_basic_update_setting($name, $value){
	$instance	= WPJAM_Basic::get_instance();

	return $instance->update_setting($name, $value);
}

function wpjam_basic_delete_setting($name){
	$instance	= WPJAM_Basic::get_instance();

	return $instance->delete_setting($name);
}

function wpjam_basic_get_default_settings(){
	return WPJAM_Basic::get_default_settings();
}

function wpjam_add_basic_sub_page($sub_slug, $args=[]){
	WPJAM_Basic::add_sub_page($sub_slug, $args);
}

function wpjam_get_extends(){
	$instance	= WPJAM_Extends::get_instance();
	return $instance->get_settings();
}

function wpjam_has_extend($extend){
	$instance	= WPJAM_Extends::get_instance();
	return $instance->has($extend);
}