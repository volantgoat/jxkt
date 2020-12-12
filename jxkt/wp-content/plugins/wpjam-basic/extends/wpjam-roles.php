<?php
/*
Name: 用户角色
URI: https://blog.wpjam.com/m/wpjam-roles/
Description: 用户角色管理，以及用户额外权限设置。
Version: 1.0
*/
if(is_admin()){
	wpjam_add_menu_page('roles', [
		'parent'		=> 'users',
		'menu_title'	=> '角色管理',
		'function'		=> 'list',
		'order'			=> 8,
		'capability'	=> is_multisite() ? 'manage_site':'manage_options'
	]);

	add_action('wpjam_builtin_page_load', function ($screen_base){
		if(in_array($screen_base, ['user-edit', 'profile'])){
			add_filter('additional_capabilities_display', '__return_false' );

			function wpjam_edit_user_capabilities_profile($profileuser){
				$capability	= is_multisite() ? 'manage_sites' : 'manage_options';

				if(current_user_can($capability)){
					$capabilities	= wpjam_get_additional_capabilities($profileuser);

					echo '<h3>额外权限</h3>';

					$form_fields = array(
						'capabilities'	=> array('title'=>'权限',	'type'=>'mu-text',	'value'=>$capabilities),
					);

					wpjam_fields($form_fields); 
				}
			}
			add_action('show_user_profile','wpjam_edit_user_capabilities_profile');
			add_action('edit_user_profile','wpjam_edit_user_capabilities_profile');


			function wpjam_edit_user_capabilities_profile_update($user_id){

				$capability	= is_multisite() ? 'manage_sites' : 'manage_options';

				if(current_user_can($capability)){
					$user			= get_userdata($user_id);
					$capabilities	= wpjam_get_parameter('capabilities',	['method'=>'POST', 'default'=>[]]);;

					$capabilities	= array_diff($capabilities, ['manage_sites', 'manage_options']);

					wpjam_set_additional_capabilities($user, $capabilities);
				}
			}
			add_action('personal_options_update','wpjam_edit_user_capabilities_profile_update');
			add_action('edit_user_profile_update','wpjam_edit_user_capabilities_profile_update');
		}
	});

	add_action('wpjam_plugin_page_load', function($plugin_page){
		if($plugin_page != 'roles'){
			return;
		}

		wpjam_register_list_table('roles', [
			'title'				=> '用户角色',
			'singular'			=> 'wpjam-role',
			'plural'			=> 'wpjam-roles',
			'primary_column'	=> 'role',
			'primary_key'		=> 'role',
			'model'				=> 'WPJAM_AdminRole',
			'ajax'				=> true,
		]);

		class WPJAM_AdminRole  {
			public static function get($role){
				global $wp_roles;
				$roles	= $wp_roles->roles;

				$arr	= $roles[$role] ?? [];

				$user_counts	= count_users();
				$user_counts	= $user_counts['avail_roles'];

				if($arr){
					$arr['role']				= $role;
					$arr['capabilities']		= array_keys($arr['capabilities']);
					$arr['capabilities_count']	= count($arr['capabilities']);
					$arr['user_count']			= isset($user_counts[$role])?('<a href="'.admin_url('users.php?role='.$role).'">'.$user_counts[$role].'</a>'):0;
				}

				return $arr;
			}

			public static function prepare($data){
				$capabilities	= [];

				if($data['capabilities']){
					foreach ($data['capabilities'] as $capability) {
						if($capability){
							$capabilities[$capability]	= 1;
						}
					}
				}

				$data['capabilities']	= $capabilities;

				return $data;
			}

			public static function insert($data){

				$data	= self::prepare($data);

				$role			= $data['role'];
				$name			= $data['name'];
				$capabilities	= $data['capabilities'];

				$result	= add_role($role, $name, $capabilities);

				if($result == null){
					return new WP_Error('insert_error', '新建失败，可能重名或者其他原因。');
				}

				return $role;
			}

			public static function update($role, $data){
				$data	= self::prepare($data);

				$name			= $data['name'];
				$capabilities	= $data['capabilities'];

				remove_role($role);
				$result	= add_role($role, $name, $capabilities);

				if($result == null){
					return new WP_Error('insert_error', '修改失败，可能重名或者其他原因。');
				}

				return true;
			}

			public static function delete($role){
				if($role == 'administrator'){
					return  new WP_Error('delete_error', '不能超级管理员角色。');
				}

				return remove_role($role);
			}

			public static function reset(){
				require_once ABSPATH . 'wp-admin/includes/schema.php';

				global $wp_roles;
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}

				foreach ($wp_roles->roles as $role_name=>$role_info){
					// $role = get_role($role_name);

					// foreach ($role_info['capabilities'] as $capability=>$_){
					// 	$role->remove_cap($capability);
					// }

					remove_role($role_name);
				}

				populate_roles();
			}

			// 后台 list table 显示
			public static function list($limit, $offset){
				global $wp_roles;

				$roles 	= $wp_roles->roles;

				$items	= [];

				$user_counts	= count_users();
				$user_counts	= $user_counts['avail_roles'];

				foreach ($roles as $key=>$role) {
					$role['role']				= $key;
					$role['name']				= translate_user_role($role['name']);
					$role['user_count']			= isset($user_counts[$key])?('<a href="'.admin_url('users.php?role='.$key).'">'.$user_counts[$key].'</a>'):0;
					$role['capabilities_count']	= count($role['capabilities']);

					$items[]	= $role;
				}

				$total = count($items);

				return compact('items', 'total');
			}

			public static function item_callback($item){
				if($item['role'] == 'administrator'){
					unset($item['row_actions']['delete']);
				}
				return $item;
			}

			public static function get_fields($action_key='', $id=0){
				$fields = [
					'role'				=> ['title'=>'角色',		'type'=>'text',		'show_admin_column'=>true],
					'name'				=> ['title'=>'名称',		'type'=>'text',		'show_admin_column'=>true],
					'capabilities'		=> ['title'=>'权限',		'type'=>'mu-text'],
					'user_count'		=> ['title'=>'用户数',	'type'=>'view',		'show_admin_column'=>'only'],
					'capabilities_count'=> ['title'=>'权限',		'type'=>'view',		'show_admin_column'=>'only'],
				];

				if($action_key == 'edit'){
					$fields['role']['type']	= 'view';
				}

				return $fields;
			}

			public static function get_actions(){
				return [
					'add'		=> ['title'=>'新建'],
					'edit'		=> ['title'=>'编辑'],
					'delete'	=> ['title'=>'删除',	'bulk'=>true,		'direct'=>true,	'confirm'=>true],
					'reset'		=> ['title'=>'重置',	'overall'=>true,	'direct'=>true,	'confirm'=>true]
				];
			}
		}
	});
}

function wpjam_get_additional_capabilities($user){
	global $wp_roles;

	$capabilities	= [];

	foreach ($user->caps as $cap => $value) {
		if($value && !$wp_roles->is_role($cap)){
			$capabilities[]	= $cap;
		}
	}

	return $capabilities;
}

function wpjam_set_additional_capabilities($user, $capabilities){
	$capabilities		= array_diff($capabilities, ['manage_sites', 'manage_options']);
	$old_capabilities 	= wpjam_get_additional_capabilities($user);

	$remove_capabilities	= array_diff($old_capabilities, $capabilities);
	$add_capabilities		= array_diff($capabilities, $old_capabilities);

	if($remove_capabilities){
		foreach ($remove_capabilities as $cap) {
			$user->remove_cap($cap);
		}
	}

	if($add_capabilities){
		foreach ($add_capabilities as $cap) {
			$user->add_cap($cap);
		}
	}

	return $capabilities;
}