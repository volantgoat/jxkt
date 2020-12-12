<?php
/*
Name: 百度站长
URI: https://blog.wpjam.com/m/baidu-zz/
Description: 支持主动，被动，自动以及批量方式提交链接到百度站长。
Version: 1.0
*/
class WPJAM_Baidu_ZZ{
	public static function notify($urls, $args=[]){
		$query_args	= [];

		$query_args['site']		= wpjam_get_setting('baidu-zz', 'site');
		$query_args['token']	= wpjam_get_setting('baidu-zz', 'token');

		if(empty($query_args['site']) || empty($query_args['token'])){
			return;
		}

		$update	= $args['update'] ?? false;
		$type	= $args['type'] ?? '';

		if(empty($type) && wpjam_get_setting('baidu-zz', 'mip')){
			$type	= 'mip';
		}

		if($type){
			$query_args['type']	= $type;
		}

		if($update){
			$baidu_zz_api_url	= add_query_arg($query_args, 'http://data.zz.baidu.com/update');
		}else{
			$baidu_zz_api_url	= add_query_arg($query_args, 'http://data.zz.baidu.com/urls');
		}

		return wp_remote_post($baidu_zz_api_url, array(
			'headers'	=> ['Accept-Encoding'=>'','Content-Type'=>'text/plain'],
			'sslverify'	=> false,
			'blocking'	=> false,
			'body'		=> $urls
		));
	}

	public static function notify_post_urls($post_id){
		$urls	= '';

		if(is_array($post_id)){
			$post_ids	= $post_id;

			foreach ($post_ids as $post_id) {
				if(get_post($post_id)->post_status == 'publish'){
					if(wp_cache_get($post_id, 'wpjam_baidu_zz_notified') === false){
						wp_cache_set($post_id, true, 'wpjam_baidu_zz_notified', HOUR_IN_SECONDS);
						$urls	.= apply_filters('baiduz_zz_post_link', get_permalink($post_id))."\n";
					}
				}
			}
		}else{
			if(get_post($post_id)->post_status == 'publish'){
				if(wp_cache_get($post_id, 'wpjam_baidu_zz_notified') === false){
					wp_cache_set($post_id, true, 'wpjam_baidu_zz_notified', HOUR_IN_SECONDS);
					$urls	.= apply_filters('baiduz_zz_post_link', get_permalink($post_id))."\n";
				}else{
					return new WP_Error('has_submited', '一小时内已经提交过了');
				}
			}else{
				return new WP_Error('invalid_post_status', '未发布的文章不能同步到百度站长');
			}
		}

		if($urls){
			self::notify($urls);
		}else{
			return new WP_Error('empty_urls', '没有需要提交的链接');
		}

		return true;
	}

	public static function ajax_submit(){
		$offset	= wpjam_get_data_parameter('offset',	['default'=>0, 'sanitize_callback'=>'intval']);
		$type	= wpjam_get_data_parameter('type',		['default'=>'post']);

		// $types	= apply_filters('wpjam_baidu_zz_batch_submit_types', ['post']);

		// if($type){
		// 	$index	= array_search($type, $types);
		// 	$types	= array_slice($types, $index, -1);
		// }

		// foreach ($types as $type) {
			if($type=='post'){
				$_query	= new WP_Query([
					'post_type'			=>'any',
					'post_status'		=>'publish',
					'posts_per_page'	=>100,
					'offset'			=>$offset
				]);

				if($_query->have_posts()){
					$count	= count($_query->posts);
					$number	= $offset+$count;

					$urls	= '';

					while($_query->have_posts()){
						$_query->the_post();

						if(wp_cache_get(get_the_ID(), 'wpjam_baidu_zz_notified') === false){
							wp_cache_set(get_the_ID(), true, 'wpjam_baidu_zz_notified', HOUR_IN_SECONDS);
							$urls	.= apply_filters('baiduz_zz_post_link', get_permalink())."\n";
						}
					}

					WPJAM_Baidu_ZZ::notify($urls);

					$args	= http_build_query(['type'=>$type, 'offset'=>$number]);

					return ['done'=>0, 'errmsg'=>'批量提交中，请勿关闭浏览器，已提交了'.$number.'个页面。',	'args'=>$args];
				}else{
					return true;
				}
			}else{
				// do_action('wpjam_baidu_zz_batch_submit', $type, $offset);
				// wpjam_send_json();
			}
		// }
	}

	public static function get_fields(){
		return [
			'site'	=>['title'=>'站点 (site)',	'type'=>'text',	'class'=>'all-options'],
			'token'	=>['title'=>'密钥 (token)',	'type'=>'password'],
			'mip'	=>['title'=>'MIP',			'type'=>'checkbox', 'description'=>'博客已支持MIP'],
			'no_js'	=>['title'=>'不加载推送JS',	'type'=>'checkbox', 'description'=>'插件已支持主动推送，不加载百度推送JS'],
		];
	}

	public static function on_save_post($post_id, $post, $update){
		if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)){
			return;
		}

		if(!$update && $post->post_status == 'publish'){
			$post_link	= apply_filters('baiduz_zz_post_link', get_permalink($post_id), $post_id);

			$args	= [];

			if(wpjam_get_parameter('baidu_zz_daily',	['method'=>'POST'])){
				$args['type']	= 'daily';
			}

			self::notify($post_link, $args);
		}
	}

	public static function on_post_updated($post_id, $post_after, $post_before){
		if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)){
			return;
		}

		if($post_after->post_status == 'publish'){

			$baidu_zz_daily	= wpjam_get_parameter('baidu_zz_daily',	['method'=>'POST']);

			if($baidu_zz_daily || wp_cache_get($post_id, 'wpjam_baidu_zz_notified') === false){
				wp_cache_set($post_id, true, 'wpjam_baidu_zz_notified', HOUR_IN_SECONDS);

				$post_link	= apply_filters('baiduz_zz_post_link', get_permalink($post_id), $post_id);

				$args	= [];

				if($baidu_zz_daily){
					$args['type']	= 'daily';
				}

				self::notify($post_link, $args);
			}
		}
	}

	public static function on_publish_future_post($post_id){
		$urls	= apply_filters('baiduz_zz_post_link', get_permalink($post_id))."\n";

		wp_cache_set($post_id, true, 'wpjam_baidu_zz_notified', HOUR_IN_SECONDS);

		self::notify($urls);
	}

	public static function on_enqueue_scripts(){
		if(wpjam_get_setting('baidu-zz', 'no_js')){
			return;
		}

		if(is_404() || is_preview()){
			return;
		}elseif(is_singular() && get_post_status() != 'publish'){
			return;
		}

		if(is_ssl()){
			wp_enqueue_script('baidu_zz_push', 'https://zz.bdstatic.com/linksubmit/push.js', '', '', true);
		}else{
			wp_enqueue_script('baidu_zz_push', 'http://push.zhanzhang.baidu.com/push.js', '', '', true);
		}
	}

	public static function on_admin_enqueue_scripts(){
		wp_add_inline_style('wpjam-style', '#post-body #baidu_zz_section:before {content: "\f103"; color:#82878c; font: normal 20px/1 dashicons; speak: none; display: inline-block; margin-left: -1px; padding-right: 3px; vertical-align: top; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }');
	}

	public static function on_post_submitbox_misc_actions(){ ?>
		<div class="misc-pub-section" id="baidu_zz_section">
			<input type="checkbox" name="baidu_zz_daily" id="baidu_zz" value="1">
			<label for="baidu_zz_daily">提交给百度站长快速收录</label>
		</div>
	<?php }
}

function wpjam_notify_baidu_zz($urls, $args=[]){
	return WPJAM_Baidu_ZZ::notify($urls, $args);
}

add_action('publish_future_post', ['WPJAM_Baidu_ZZ', 'on_publish_future_post'], 11);

if(!is_admin()){
	add_action('wp_enqueue_scripts', ['WPJAM_Baidu_ZZ', 'on_enqueue_scripts']);
}else{
	wpjam_add_basic_sub_page('baidu-zz', [
		'menu_title'	=>'百度站长',
		'function'		=>'tab',
		'summary'		=>'百度站长扩展实现提交链接到百度站长，让博客的文章能够更快被百度收录，详细介绍请点击：<a href="https://blog.wpjam.com/m/301-redirects/" target="_blank">百度站长</a>。',
		'tabs'			=>[
			'baidu-zz'	=>[
				'title'			=>'百度站长',
				'function'		=>'option',
				'option_name'	=>'baidu-zz',
			],
			'batch'		=>[
				'title'		=>'批量提交',
				'function'	=>'form',
				'form_name'	=>'baidu_zz_submit_pages',
				'summary'	=>'使用百度站长更新内容接口批量将博客中的所有内容都提交给百度搜索资源平台。'
			],
		]
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page, $current_tab){
		if($plugin_page != 'baidu-zz' || !empty($current_tab)){
			return;
		}

		wpjam_register_option('baidu-zz', [
			'title'		=> '', 
			'fields'	=> ['WPJAM_Baidu_ZZ', 'get_fields']
		]);

		wpjam_register_page_action('baidu_zz_submit_pages', [
			'submit_text'	=> '批量提交',
			'callback'		=> ['WPJAM_Baidu_ZZ', 'ajax_submit']
		]);
	}, 10, 2);

	add_action('wpjam_builtin_page_load', function($screen_base, $current_screen){
		if($screen_base == 'edit'){
			if(!is_post_type_viewable($current_screen->post_type)){
				return;
			}

			wpjam_register_list_table_action('baidu-zz', [
				'title'			=>'提交到百度', 
				'bulk'			=>true,
				'direct'		=>true,
				'post_status'	=>['publish'],
				'callback'		=>['WPJAM_Baidu_ZZ', 'notify_post_urls']
			]);
		}elseif($screen_base == 'post'){
			if(!is_post_type_viewable($current_screen->post_type)){
				return;
			}

			add_action('save_post', 	['WPJAM_Baidu_ZZ', 'on_save_post'], 10, 3);
			add_action('post_updated',	['WPJAM_Baidu_ZZ', 'on_post_updated'], 10, 3);

			add_action('post_submitbox_misc_actions',	['WPJAM_Baidu_ZZ', 'on_post_submitbox_misc_actions'],11);
			add_action('admin_enqueue_scripts',			['WPJAM_Baidu_ZZ', 'on_admin_enqueue_scripts']);
		}
	}, 10, 2);
}