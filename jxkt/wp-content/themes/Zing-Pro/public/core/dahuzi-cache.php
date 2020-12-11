<?php

// 设置 WP Query 缓存
function dahuzi_query($args=[], $cache_time='3600'){
	$args['no_found_rows']	= true;
	$args['cache_results']	= true;

	$args['cache_it']	= true;

	return new WP_Query($args);
}

//
class Dahuzi_Post{

	public static function get_by_ids($post_ids){
		return self::update_caches($post_ids);
	}
	
	public static function update_caches($post_ids, $args=[]){
		if($post_ids){
			$post_ids 	= array_filter($post_ids);
			$post_ids 	= array_unique($post_ids);
		}

		if(empty($post_ids)) {
			return [];
		}

		$update_term_cache	= true;
		$update_meta_cache	= true;

		_prime_post_caches($post_ids, $update_term_cache, $update_meta_cache);

		$caches	= [];

		foreach ($post_ids as $post_id) {
			$cache	= wp_cache_get($post_id, 'posts');
			if($cache !== false){
				$caches[$post_id]	= $cache;
			}
		}

		return $caches;
	}

}


function dahuzi_get_posts($post_ids, $args=[]){
	$posts = Dahuzi_Post::get_by_ids($post_ids, $args);
	return $posts ? array_values($posts) : [];
}


add_action('parse_query', function (&$wp_query){
	if(!is_admin() && $wp_query->get('post_type') == 'nav_menu_item'){	// 让菜单也支持缓存
		$wp_query->set('suppress_filters', false);
	}
});

add_filter('posts_pre_query', function ($pre, $wp_query){
	if($wp_query->get('orderby') == 'rand'){	// 随机排序就不能缓存了
		return $pre;
	}

	if(!$wp_query->is_main_query() && $wp_query->get('post_type') != 'nav_menu_item' && !$wp_query->get('cache_it') ){	// 只缓存主循环 || 菜单 || 要求缓存的
		return $pre;
	}

	$key			= md5(serialize(array_filter($wp_query->query_vars)).$wp_query->request);
	$last_changed	= wp_cache_get_last_changed('posts');
	$cache_key		= "dahuzi_get_posts:$key:$last_changed";

	$post_ids		= wp_cache_get($cache_key, 'dahuzi_post_ids');

	$wp_query->set('cache_key', $cache_key);
	
	if($post_ids === false){
		return $pre;
	}

	if($post_ids && !$wp_query->is_singular() && empty($wp_query->get('nopaging')) && empty($wp_query->get('no_found_rows'))){	// 如果需要缓存总数
		$found_posts	= wp_cache_get($cache_key, 'dahuzi_found_posts');

		if($found_posts === false){
			return $pre;
		}

		$wp_query->set('no_found_rows', true);

		$wp_query->found_posts		= $found_posts;
		$wp_query->max_num_pages	= ceil($found_posts/$wp_query->get('posts_per_page'));
	}

	$args	= wp_array_slice_assoc($wp_query->query_vars, ['update_post_term_cache', 'update_post_meta_cache']);

	return dahuzi_get_posts($post_ids, $args);	
}, 10, 2); 

add_filter('posts_results',	 function ($posts, $wp_query) {
	$cache_key	= $wp_query->get('cache_key');

	if($cache_key){
		if(count($posts)>1){
			$post_authors	= wp_list_pluck($posts, 'post_author');
			$post_authors	= array_unique($post_authors);
			$post_authors	= array_filter($post_authors);

			if(count($post_authors)>1){
				cache_users($post_authors);
			}
		}

		$post_ids	= wp_cache_get($cache_key, 'dahuzi_post_ids');
		if($post_ids === false){
			wp_cache_set($cache_key, array_column($posts, 'ID'), 'dahuzi_post_ids', HOUR_IN_SECONDS);
		}
	}

	return $posts;
}, 10, 2);

add_filter('found_posts', function ($found_posts, $wp_query) {
	$cache_key	= $wp_query->get('cache_key');

	if($cache_key){
		wp_cache_set($cache_key, $found_posts, 'dahuzi_found_posts', HOUR_IN_SECONDS);
	}
		
	return $found_posts;
}, 10, 2);


// 使用内存来存储和获取自定义字段信息
add_filter('update_post_metadata', function($check, $post_id, $meta_key, $meta_value){
	if($meta_key == '_edit_lock'){
		return wp_cache_set($post_id, $meta_value, '_edit_lock', 300);
	}elseif($meta_key == '_edit_last'){
		if(get_post($post_id)->post_author == $meta_value){
			if(get_post_meta($post_id, $meta_key, true) != $meta_value){
				delete_post_meta($post_id, $meta_key);
			}
			
			return true;
		}
	}

	return $check;
}, 1, 4);


add_filter('add_post_metadata', function($check, $post_id, $meta_key, $meta_value){
	if($meta_key == '_edit_lock'){
		return wp_cache_set($post_id, $meta_value, '_edit_lock', 300);
	}elseif($meta_key == '_edit_last'){
		if(get_post($post_id)->post_author == $meta_value){
			return true;
		}
	}elseif($meta_key == '_wp_old_slug'){
		if(strpos($meta_value, '%') !== false){	// 含有 % 说明不是英文，含有中文和特殊字符
			return true;
		}
	}

	return $check;
}, 1, 4);

add_filter('get_post_metadata', function($pre, $post_id, $meta_key){
	$cache_keys	= ['_edit_lock'];
	
	if(in_array($meta_key, $cache_keys)){
		$meta_value	= wp_cache_get($post_id, $meta_key);

		if($meta_value !== false){
			return [$meta_value];
		}
	}elseif($meta_key == '_edit_last'){
		$meta_values	= get_post_meta($post_id);

		if(!isset($meta_values['_edit_last'])){
			$meta_values['_edit_last']	= [get_post($post_id)->post_author];
		}
		
		return $meta_values['_edit_last'];
	}elseif($meta_key == ''){
		$meta_cache	= wp_cache_get($post_id, 'post_meta');

		if($meta_cache === false) {
			$meta_cache	= update_meta_cache('post', [$post_id]);
			$meta_cache	= $meta_cache[$post_id];
		}

		foreach($cache_keys as $mkey){
			$mval	= wp_cache_get($post_id, $mkey);
			if($mval !== false){
				$meta_cache[$mkey]	= [$mval];
			}
		}

		return $meta_cache;	
	}

	return $pre;
}, 1, 3);
