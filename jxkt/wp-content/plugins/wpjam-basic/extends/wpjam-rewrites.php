<?php
/*
Name: Rewrite 优化
URI: https://blog.wpjam.com/m/wpjam-rewrite/
Description: 清理无用的 Rewrite 规则和新增自定义 Rewrite 规则。
Version: 1.0
*/
class WPJAM_Rewrite{
	public static function get_all(){
		return get_option('rewrite_rules');
	}

	public static function get_rewrites(){
		return wpjam_basic_get_setting('rewrites') ?: [];
	}

	public static function update_rewrites($rewrites){
		wpjam_basic_update_setting('rewrites', $rewrites);
		flush_rewrite_rules();
		return true;
	}

	public static function get($id){
		$rewrites	= self::get_all();
		$regex_arr	= array_keys($rewrites);

		$regex		= $regex_arr[($id-1)] ?? '';

		if($regex){
			$query	= $rewrites[$regex];
			return compact('id', 'regex', 'query');
		}else{
			return [];
		}
	}

	public static function prepare($data, $id=''){
		$regex	= $data['regex'] ?? '';
		$query	= $data['query'] ?? '';

		if(empty($regex) || empty($query)){
			return new WP_error('empty_regex', 'Rewrite 规则不能为空');
		}

		$rewrites	= self::get_all();

		if($id){
			$current	= self::get($id);

			if(empty($current)){
				return new WP_error('invalid_regex', '该 Rewrite 规则不存在');
			}elseif($current['regex'] != $regex && isset($rewrites[$regex])){
				return new WP_error('invalid_regex', '该 Rewrite 规则已使用');
			}
		}else{
			if(isset($rewrites[$regex])){
				return new WP_error('duplicate_regex', '该 Rewrite 规则已存在');
			}
		}

		return $data;
	}

	public static function insert($data){
		$data	= self::prepare($data);
		if(is_wp_error($data)){
			return $data;
		}

		$rewrites	= self::get_rewrites();
		$rewrites	= array_merge([$data], $rewrites);

		return self::update_rewrites($rewrites);
	}

	public static function update($id, $data){
		$data	= self::prepare($data, $id);
		if(is_wp_error($data)){
			return $data;
		}

		$current	= self::get($id);
		$rewrites	= self::get_rewrites();
		foreach ($rewrites as $i => $rewrite){
			if($rewrite['regex'] == $current['regex']){
				$rewrites[$i]	= $data;
				break;
			}
		}

		return self::update_rewrites($rewrites);
	}

	public static function delete($id){
		$current	= self::get($id);
		if(empty($current)){
			return new WP_error('invalid_regex', '该 Rewrite 规则不存在');
		}

		$rewrites	= self::get_rewrites();
		foreach ($rewrites as $i => $rewrite){
			if($rewrite['regex'] == $current['regex']){
				unset($rewrites[$i]);
				break;
			}
		}

		return self::update_rewrites($rewrites);
	}

	public static function query_items($limit, $offset){
		$rewrites	= self::get_all();
		$items		= [];

		$id	= 0;
		foreach ($rewrites as $regex => $query) {
			$id++;
			$items[]	= compact('id', 'regex', 'query');
		}

		$total	= count($items);

		// wpjam_print_r(get_defined_constants(true));
		// wpjam_print_r(get_defined_functions(true));
		// wpjam_print_r(get_declared_classes());
		// wpjam_print_r(get_declared_interfaces());
		// wpjam_print_r(get_defined_vars());

		return compact('items', 'total');
	}

	public static function item_callback($item){
		$rewrites	= self::get_rewrites();

		$rewrites	= $rewrites ? wp_list_pluck($rewrites, 'query', 'regex') : [];
		if(!$rewrites || !isset($rewrites[$item['regex']])){
			unset($item['row_actions']);
			$item['regex']	= wpautop($item['regex']);
		}

		$item['query']	= wpautop($item['query']);

		return $item;
	}

	public static function get_actions(){
		return [
			'add'		=> ['title'=>'新建',	'response'=>'list'],
			'edit'		=> ['title'=>'编辑'],
			'delete'	=> ['title'=>'删除',	'direct'=>true,	'response'=>'list']
		];
	}

	public static function get_fields($action_key='', $id=0){
		return [
			'regex'		=> ['title'=>'正则',		'type'=>'text',	'show_admin_column'=>true],
			'query'		=> ['title'=>'查询',		'type'=>'text',	'show_admin_column'=>true]
		];
	}



	public static function remove_rules(&$rules){
		$unuse_rewrite_keys = ['comment-page', 'feed=', 'attachment'];

		foreach ($unuse_rewrite_keys as $i=>$unuse_rewrite_key) {
			if(wpjam_basic_get_setting('remove_'.$unuse_rewrite_key.'_rewrite') == false){
				unset($unuse_rewrite_keys[$i]);
			}
		}

		if(wpjam_basic_get_setting('disable_post_embed')){
			$unuse_rewrite_keys[]	= '&embed=true';
		}

		if(wpjam_basic_get_setting('disable_trackbacks')){
			$unuse_rewrite_keys[]	= '&tb=1';
		}

		if($unuse_rewrite_keys){
			foreach ($rules as $key => $rule) {
				if($rule == 'index.php?&feed=$matches[1]'){
					continue;
				}

				foreach ($unuse_rewrite_keys as $unuse_rewrite_key) {
					if(strpos($key, $unuse_rewrite_key) !== false || strpos($rule, $unuse_rewrite_key) !== false){
						unset($rules[$key]);
					}
				}
			}
		}
	}

	public static function on_generate_rewrite_rules($wp_rewrite){
		self::remove_rules($wp_rewrite->rules); 
		self::remove_rules($wp_rewrite->extra_rules_top);

		if($wpjam_rewrites	= wpjam_basic_get_setting('rewrites')){
			$wpjam_rules	= wp_list_pluck($wpjam_rewrites, 'query', 'regex');
			$wp_rewrite->rules = array_merge($wpjam_rules, $wp_rewrite->rules);
		}
	}

	public static function on_init(){
		if(wpjam_basic_get_setting('remove_date_rewrite')){
			add_filter('date_rewrite_rules', '__return_empty_array');

			remove_rewrite_tag('%year%');
			remove_rewrite_tag('%monthnum%');
			remove_rewrite_tag('%day%');
			remove_rewrite_tag('%hour%');
			remove_rewrite_tag('%minute%');
			remove_rewrite_tag('%second%');
		}

		if(wpjam_basic_get_setting('remove_comment_rewrite')){
			add_filter('comments_rewrite_rules', '__return_empty_array');
		}

		add_action('generate_rewrite_rules',	['WPJAM_Rewrite', 'on_generate_rewrite_rules']);
	}
}

add_action('init',	['WPJAM_Rewrite', 'on_init']);

if(is_admin()){
	wpjam_add_basic_sub_page('wpjam-rewrites', [
		'menu_title'	=> 'Rewrites',
		'function'		=> 'tab',
		'tabs'			=> [
			'rules'		=> ['title'=>'Rewrites 规则',	'function'=>'list'],
			'optimize'	=> ['title'=>'Rewrites 优化',	'function'=>'option',	'option_name'=>'wpjam-basic']
		],
		'summary'		=> 'Rewrites 扩展让可以优化现有 Rewrites 规则和添加额外的 Rewrite 规则，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-rewrite/" target="_blank">Rewrites 扩展</a>。'
	]);

	add_action('wpjam_plugin_page_load', function($plugin_page, $current_tab){
		if($plugin_page == 'wpjam-rewrites'){
			if($current_tab == 'rules'){
				wpjam_register_list_table('wpjam-rewrites', [
					'title'		=> 'Rewrites 规则',
					'plural'	=> 'rewrites',
					'singular' 	=> 'rewrite',
					'model'		=> 'WPJAM_Rewrite',
					'fixed'		=> false,
					'per_page'	=> 300
				]);
			}elseif($current_tab == 'optimize'){
				wpjam_register_option('wpjam-basic', [
					'summary'	=>'如果你的网站没有使用以下页面，可以移除相关功能的的 Rewrites 规则以提高网站效率！',
					'fields'	=> [
						'remove_date_rewrite'			=> ['title'=>'',	'type'=>'checkbox',	'description'=>'移除日期 Rewrite 规则'],
						'remove_comment_rewrite'		=> ['title'=>'',	'type'=>'checkbox',	'description'=>'移除留言 Rewrite 规则'],
						'remove_comment-page_rewrite'	=> ['title'=>'',	'type'=>'checkbox',	'description'=>'移除留言分页 Rewrite 规则'],
						'remove_feed=_rewrite'			=> ['title'=>'',	'type'=>'checkbox',	'description'=>'移除分类 Feed Rewrite 规则'],
						'remove_attachment_rewrite'		=> ['title'=>'',	'type'=>'checkbox',	'description'=>'移除附件 Rewrite 规则'],
						// 'remove_type/_rewrite'		=> '文章格式Rewrite规则',
						// 'remove_author_rewrite'		=> '作者Rewrite规则',
					]
				]);

				flush_rewrite_rules();
			}
		}
	}, 10, 2);
}