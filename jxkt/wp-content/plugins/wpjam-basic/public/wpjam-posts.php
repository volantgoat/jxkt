<?php
class WPJAM_AdminPost{
	public static function get_views($post_id){
		$post_views	= wpjam_get_post_views($post_id, false);
		$post_type	= get_post($post_id)->post_type;

		if(current_user_can(get_post_type_object($post_type)->cap->edit_others_posts)){
			$post_views	= wpjam_get_list_table_row_action('update_views',[
				'id'	=> $post_id,
				'title'	=> $post_views ?: 0,
			]);	
		}

		return $post_views;
	}

	public static function get_thumbnail($post_id){
		$thumbnail	= get_the_post_thumbnail($post_id, [50,50]) ?: '<span class="no-thumbnail">暂无图片</span>';
		$post_type	= get_post($post_id)->post_type;

		if(post_type_supports($post_type, 'thumbnail') && current_user_can('edit_post', $post_id)){
			$thumbnail = wpjam_get_list_table_row_action('set_thumbnail',['id'=>$post_id, 'title'=>$thumbnail]);
		}

		return $thumbnail;
	}

	public static function update_views($post_id, $data){
		return isset($data['views']) ? update_post_meta($post_id, 'views', $data['views']) : true;
	}

	public static function set_thumbnail($post_id, $data){
		return WPJAM_Post::update_meta($post_id, '_thumbnail_id', $data['_thumbnail_id']);
	}

	public static function filter_map_meta_cap($caps, $cap, $user_id, $args){
		if($cap == 'edit_post'){
			if(empty($args[0])){
				$post_type	= get_current_screen()->post_type;
				$pt_obj		= get_post_type_object($post_type);

				return !$pt_obj->map_meta_cap ? [$pt_obj->cap->$cap] : [$pt_obj->cap->edit_posts];
			}
		}
		
		return $caps;
	}

	public static function filter_html($html){
		$post_type	= get_current_screen()->post_type;

		if(!wp_doing_ajax()){
			if(wpjam_has_extend('quick-excerpt') && post_type_supports($post_type, 'excerpt')){
				$excerpt_inline_edit	= '
				<label>
					<span class="title">摘要</span>
					<span class="input-text-wrap"><textarea cols="22" rows="2" name="the_excerpt"></textarea></span>
				</label>
				';

				$html	= str_replace('<fieldset class="inline-edit-date">', $excerpt_inline_edit.'<fieldset class="inline-edit-date">', $html);
			}
		}

		if(!wp_doing_ajax() || (wp_doing_ajax() && $_POST['action'] == 'inline-save')){
			if(wpjam_basic_get_setting('post_list_set_thumbnail') && (is_post_type_viewable($post_type) || post_type_supports($post_type, 'thumbnail'))){	
				if(preg_match_all('/<tr id="post-(\d+)" class=".*?">.*?<\/tr>/is', $html, $matches)){
					$search	= $replace = $matches[0];

					foreach ($matches[1] as $i => $post_id){
						$replace[$i]	= str_replace('<a class="row-title"', self::get_thumbnail($post_id).'<a class="row-title"', $replace[$i]);
					}

					$html	= str_replace($search, $replace, $html);
				}
			}
		}

		return $html;
	}

	public static function on_add_inline_data($post){
		$post_type	= $post->post_type;

		if(wpjam_has_extend('quick-excerpt') && post_type_supports($post_type, 'excerpt')){
			echo '<div class="post_excerpt">' . esc_textarea(trim($post->post_excerpt)) . '</div>';
		}

		if(wpjam_basic_get_setting('post_list_set_thumbnail') && (is_post_type_viewable($post_type) || post_type_supports($post_type, 'thumbnail'))){
			echo '<div class="post_thumbnail">' . self::get_thumbnail($post->ID) . '</div>';
		}
	}

	public static function filter_insert_post_data($data, $postarr){
		if(wpjam_has_extend('quick-excerpt') && post_type_supports($data['post_type'], 'excerpt')){
			if(isset($_POST['the_excerpt'])){
				$data['post_excerpt']   = $_POST['the_excerpt'];
			}
		}
			
		return $data;
	}

	public static function filter_posts_clauses($clauses, $wp_query){
		if($wp_query->is_main_query() && $wp_query->is_search()){
			global $wpdb;

			$search_term	= $wp_query->query['s'];

			if(is_numeric($search_term)){
				$clauses['where'] = str_replace('('.$wpdb->posts.'.post_title LIKE', '('.$wpdb->posts.'.ID = '.$search_term.') OR ('.$wpdb->posts.'.post_title LIKE', $clauses['where']);
			}elseif(preg_match("/^(\d+)(,\s*\d+)*\$/", $search_term)){
				$clauses['where'] = str_replace('('.$wpdb->posts.'.post_title LIKE', '('.$wpdb->posts.'.ID in ('.$search_term.')) OR ('.$wpdb->posts.'.post_title LIKE', $clauses['where']);
			}

			if($search_metas = $wp_query->get('search_metas')){
				$clauses['where']	= preg_replace_callback('/\('.$wpdb->posts.'.post_title LIKE (.*?)\) OR/', function($matches) use($search_metas){
					global $wpdb;
					$search_metas	= "'".implode("', '", $search_metas)."'";

					return "EXISTS (SELECT * FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id={$wpdb->posts}.ID AND meta_key IN ({$search_metas}) AND meta_value LIKE ".$matches[1].") OR ".$matches[0];
				}, $clauses['where']);
			}
		}

		return $clauses;
	}

	public static function on_restrict_manage_posts($post_type){
		if($taxonomies	= get_object_taxonomies($post_type, 'objects')){
			foreach($taxonomies as $taxonomy) {

				if(empty($taxonomy->show_admin_column)){
					continue;
				}

				if($taxonomy->name == 'category'){
					if(isset($taxonomy->filterable) && !$taxonomy->filterable){
						continue;
					}

					$taxonomy_key	= 'cat';
				}else{
					if(empty($taxonomy->filterable)){
						continue;
					}

					if($taxonomy->name == 'post_tag'){
						$taxonomy_key	= 'tag_id';
					}else{
						$taxonomy_key	= $taxonomy->name.'_id';
					}
				}

				$selected	= 0;

				if(!empty($_REQUEST[$taxonomy_key])){
					$selected	= intval($_REQUEST[$taxonomy_key]);
				}elseif(!empty($_REQUEST['taxonomy']) && ($_REQUEST['taxonomy'] == $taxonomy->name) && !empty($_REQUEST['term'])){
					if($term	= get_term_by('slug', $_REQUEST['term'], $taxonomy->name)){
						$selected	= $term->term_id;
					}
				}elseif(!empty($taxonomy->query_var) && !empty($_REQUEST[$taxonomy->query_var])){
					if($term	= get_term_by('slug', $_REQUEST[$taxonomy->query_var], $taxonomy->name)){
						$selected	= $term->term_id;
					}
				}

				if($taxonomy->hierarchical){
					wp_dropdown_categories(array(
						'taxonomy'			=> $taxonomy->name,
						'show_option_all'	=> $taxonomy->labels->all_items,
						'show_option_none'	=> '没有设置',
						'hide_if_empty'		=> true,
						'hide_empty'		=> 0,
						'hierarchical'		=> 1,
						'show_count'		=> 0,
						'orderby'			=> 'name',
						'name'				=> $taxonomy_key,
						'selected'			=> $selected
					));
				}else{
					echo wpjam_get_field_html([
						'title'			=> '',
						'key'			=> $taxonomy_key,
						'type'			=> 'text',
						'class'			=> '',
						'value'			=> $selected ?: '',
						'placeholder'	=> '请输入'.$taxonomy->label,
						'data_type'		=> 'taxonomy',
						'taxonomy'		=> $taxonomy->name
					]);
				}
			}
		}

		if(wpjam_basic_get_setting('post_list_author_filter') && post_type_supports($post_type, 'author')){
			wp_dropdown_users([
				'name'						=> 'author',
				'who'						=> 'authors',
				'show_option_all'			=> '所有作者',
				'hide_if_only_one_author'	=> true,
				'selected'					=> wpjam_get_parameter('author', ['method'=>'REQUEST', 'sanitize_callback'=>'intval'])
			]);
		}

		if(wpjam_basic_get_setting('post_list_sort_selector')){
			$wp_list_table		= $GLOBALS['wp_list_table'] ?: _get_list_table('WP_Posts_List_Table', ['screen'=>$post_type]);
			$orderby_options	= [
				''			=> '排序',
				'date'		=> '日期', 
				'modified'	=> '修改时间',
				'ID'		=> get_post_type_object($post_type)->labels->name.'ID',
				'title'		=> '标题', 
			];

			if(post_type_supports($post_type, 'comments')){
				$orderby_options['comment_count']	= '评论';
			}

			if(is_post_type_hierarchical($post_type)){
				// $orderby_options['parent']	= '父级';
			}

			list($columns, $hidden, $sortable_columns, $primary) = $wp_list_table->get_column_info();

			$default_sortable_columns	= $wp_list_table->get_sortable_columns();

			foreach($sortable_columns as $sortable_column => $data){
				if(isset($default_sortable_columns[$sortable_column])){
					continue;
				}

				if(isset($columns[$sortable_column])){
					$orderby_options[$sortable_column]	= $columns[$sortable_column];
				}
			}

			echo wpjam_get_field_html([
				'title'		=>'',
				'key'		=>'orderby',
				'type'		=>'select',
				'value'		=>wpjam_get_parameter('orderby', ['method'=>'REQUEST', 'sanitize_callback'=>'sanitize_key']),
				'options'	=>$orderby_options
			]);

			echo wpjam_get_field_html([
				'title'		=>'',
				'key'		=>'order',
				'type'		=>'select',
				'value'		=>wpjam_get_parameter('order', ['method'=>'REQUEST', 'sanitize_callback'=>'sanitize_key', 'default'=>'DESC']),
				'options'	=>['desc'=>'降序','asc'=>'升序']
			]);
		}
	}
}

wpjam_add_basic_sub_page('wpjam-posts', [
	'menu_title'	=> '文章设置', 
	'summary'		=> '文章设置优化和增强文章列表和文章功能。',
	'function'		=> 'tab',
	'order'			=> 20
]);

add_action('wpjam_plugin_page_load', function($plugin_page, $current_tab){
	if($plugin_page == 'wpjam-posts'){
		wpjam_register_plugin_page_tab('posts', ['title'=>'文章列表',	'function'=>'option', 	'option_name'=>'wpjam-basic']);

		if($current_tab == 'posts'){
			wpjam_register_option('wpjam-basic', [
				'summary'	=> '文章设置把文章编辑的一些常用操作，提到文章列表页面，方便设置和操作，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-posts/" target="_blank">文章设置</a>。',
				'fields'	=> [
					'post_list_set_thumbnail'	=> ['title'=>'缩略图',	'type'=>'checkbox',	'description'=>'在文章列表页显示和设置文章缩略图。'],
					'post_list_update_views'	=> ['title'=>'浏览数',	'type'=>'checkbox',	'description'=>'在文章列表页显示和修改文章浏览数。'],
					'post_list_author_filter'	=> ['title'=>'作者过滤',	'type'=>'checkbox',	'description'=>'在文章列表页支持通过作者进行过滤。'],
					'post_list_sort_selector'	=> ['title'=>'排序选择',	'type'=>'checkbox',	'description'=>'在文章列表页显示排序下拉选择框。'],
				], 
				
			]);
		}	
	}
}, 10, 2);

add_action('wpjam_builtin_page_load', function($screen_base){
	if($screen_base == 'post'){
		if(wpjam_basic_get_setting('disable_block_editor')){
			add_filter('use_block_editor_for_post_type', '__return_false');
		}else{
			if(wpjam_basic_get_setting('disable_google_fonts_4_block_editor')){	// 古腾堡编辑器不加载 Google 字体
				wp_deregister_style('wp-editor-font');
				wp_register_style('wp-editor-font', '');
			}
		}

		// if(wpjam_basic_get_setting('disable_revision')){
		//	wp_deregister_script('autosave');
		// }
		
		if(wpjam_basic_get_setting('disable_trackbacks')){
			wp_add_inline_style('wpjam-style', 'label[for="ping_status"]{display:none !important;}'."\n");
		}
	}elseif($screen_base == 'edit'){
		$post_type	= get_current_screen()->post_type;
		$pt_obj		= get_post_type_object($post_type);

		wpjam_register_list_table_action('update_views', [
			'title'			=> '修改',	
			'page_title'	=> '修改浏览数',
			'row_action'	=> false,
			'tb_width'		=> 500,
			'capability'	=> $pt_obj->cap->edit_others_posts,
			'fields'		=> ['views'	=>['title'=>'浏览数',	'type'=>'number']],
			'callback'		=> ['WPJAM_AdminPost', 'update_views']
		]);

		if(is_post_type_viewable($post_type)){
			wpjam_register_list_table_action('set_thumbnail', [
				'title'			=> '设置',	
				'page_title'	=> '设置特色图片',	
				'row_action'	=> false,
				'tb_width'		=> 500,	
				'tb_height'		=> 400,
				'fields'		=> ['_thumbnail_id'	=> ['title'=>'缩略图',	'type'=>'img',	'size'=>'600x0']],
				'callback'		=> ['WPJAM_AdminPost', 'set_thumbnail']
			]);

			if(wpjam_basic_get_setting('post_list_update_views')){
				wpjam_register_list_table_column('views', ['title'=>'浏览', 'column_callback'=>['WPJAM_AdminPost','get_views'], 'sortable_column'=>'views']);
			}

			if($post_type == 'page'){
				wpjam_register_list_table_column('template', ['title'=>'模板', 'column_callback'=>'get_page_template_slug']);
			}
		}

		add_filter('posts_clauses', 		['WPJAM_AdminPost', 'filter_posts_clauses'], 2, 2);
		add_filter('map_meta_cap',			['WPJAM_AdminPost', 'filter_map_meta_cap'], 10, 4);
		add_filter('wpjam_html',			['WPJAM_AdminPost', 'filter_html']);
		add_filter('wp_insert_post_data',	['WPJAM_AdminPost', 'filter_insert_post_data'], 10, 2);

		add_action('add_inline_data',		['WPJAM_AdminPost', 'on_add_inline_data']);
		add_action('restrict_manage_posts', ['WPJAM_AdminPost',	'on_restrict_manage_posts'], 99);

		add_filter('disable_categories_dropdown', '__return_true');

		wp_add_inline_style('list-tables', "\n".implode("\n", [
			'.tablenav .actions{padding:0 8px 8px 0;}',
			'td.column-title img.wp-post-image{float:left; margin:0px 10px 10px 0;}',
			'th.manage-column.column-views{width:72px;}',
			'.fixed .column-date{width:98px;}',
			'.fixed .column-categories, .fixed .column-tags{width:12%;}'
		])."\n");

		function wpjam_get_admin_post_list_views($post_id){
			return WPJAM_AdminPost::get_views($post_id);
		}	
	}
}, 10, 2);
