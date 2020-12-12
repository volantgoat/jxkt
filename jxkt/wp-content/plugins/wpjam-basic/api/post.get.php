<?php
global $wp, $wp_query;

if(isset($module_args)){
	$query_args	= $module_args;
}else{
	$query_args	= $args;
}

$post_id	= $query_args['id'] ?? wpjam_get_parameter('id',	['sanitize_callback'=>'intval']);
$post_type	= $query_args['post_type'] ?? wpjam_get_parameter('post_type',	['default'=>'any']);

if($post_type != 'any'){
	$pt_obj	= get_post_type_object($post_type);

	if(!$pt_obj){
		wpjam_send_json(['errcode'=>'post_type_not_exists',	'errmsg'=>'post_type 未定义']);
	}
}

if(empty($post_id)){
	if($post_type == 'any'){
		wpjam_send_json(['errcode'=>'empty_post_id',	'errmsg'=>'文章ID不能为空']);
	}

	$orderby	= wpjam_get_parameter('orderby');

	if($orderby == 'rand'){
		$wp->set_query_var('orderby', 'rand');
	}else{
		$name_key	= $pt_obj->hierarchical ? 'pagename' : 'name';

		$wp->set_query_var($name_key,	wpjam_get_parameter($name_key,	['required'=>true]));
	}
}else{
	$wp->set_query_var('p', $post_id);
}

$wp->set_query_var('post_type', $post_type);
$wp->set_query_var('posts_per_page', 1);

$wp->set_query_var('cache_results', true);
// $wp->set_query_var('update_post_meta_cache', false);
// $wp->set_query_var('update_post_term_cache', false);
// $wp->set_query_var('lazy_load_term_meta', false);

$wp->query_posts();

if($wp_query->have_posts()){
	$post_id	= $wp_query->post->ID;
}else{
	if($post_name = get_query_var('name')){
		$post_id	= apply_filters('old_slug_redirect_post_id', null);

		if(empty($post_id)){
			global $wpdb;

			$post_types	= get_post_types(['public'=>true, 'hierarchical'=>false]);
			unset($post_types['attachment']);
			$post_types	= "'".implode("','", $post_types)."'";

			$where	= $wpdb->prepare("post_name LIKE %s", $wpdb->esc_like($post_name).'%');
			$posts	= $wpdb->get_results("SELECT ID, post_type FROM $wpdb->posts WHERE $where AND post_type in ($post_types) AND post_status = 'publish'");

			if($posts){
				$post_id	= current($posts)->ID;

				if(count($posts) > 1 && $post_type && !is_null($post_type) && $post_type != 'any'){	// 指定 post_type 则获取首先获取 post_type 相同的
					$filtered_posts	= array_filter($posts, function($post) use($post_type){
						if(is_array($post_type)){
							return in_array($post->post_type, $post_type);
						}else{
							return $post->post_type == $post_type;
						}
					});

					if($filtered_posts){
						$post_id	= current($filtered_posts)->ID;
					}
				}
			}
		}

		$post_type	= 'any';

		if($post_id){
			$wp->set_query_var('post_type', $post_type);
			$wp->set_query_var('posts_per_page', 1);
			$wp->set_query_var('p', $post_id);
			$wp->set_query_var('name', '');
			$wp->set_query_var('pagename', '');

			$wp->query_posts();
		}else{
			wpjam_send_json(['errcode'=>'empty_query',	'errmsg'=>'查询结果为空']);
		}
	}else{
		wpjam_send_json(['errcode'=>'empty_query',	'errmsg'=>'查询结果为空']);
	}
}

$the_post	= wpjam_validate_post($post_id, $post_type);

if(is_wp_error($the_post)){
	wpjam_send_json($the_post);
}

if(!post_type_exists($the_post->post_type)){
	wpjam_send_json(['errcode'=>'empty_query',	'errmsg'=>'查询结果为空']);
}

$output	= $query_args['output'] ?? '';
$output	= $output ?: $the_post->post_type;

$response[$output]	= wpjam_get_post($post_id, $args);

foreach(['page_title','share_title', 'share_image'] as $key){
	if(isset($response[$output][$key])){
		if(empty($response[$key])){
			$response[$key]	= $response[$output][$key];
		}
		
		unset($response[$output][$key]);
	}
}