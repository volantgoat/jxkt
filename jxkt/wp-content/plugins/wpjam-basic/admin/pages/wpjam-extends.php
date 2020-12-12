<?php
add_filter('wpjam_extends_setting', function(){
	$fields		= [];
	$extend_dir = WPJAM_BASIC_PLUGIN_DIR.'extends';

	if(is_dir($extend_dir)) { 
		$wpjam_extends 	= wpjam_get_option('wpjam-extends');

		$file_headers	= [
			'Name'			=> 'Name',
			'URI'			=> 'URI',
			'Version'		=> 'Version',
			'Description'	=> 'Description'
		];

		if($wpjam_extends){	// 已激活的优先
			foreach ($wpjam_extends as $extend_file => $value) {
				if(!$value || !is_file($extend_dir.'/'.$extend_file)){
					continue;
				}

				$data	= get_file_data($extend_dir.'/'.$extend_file, $file_headers);

				if($data['Name']){
					$fields[$extend_file] = ['title'=>'<a href="'.$data['URI'].'" target="_blank">'.$data['Name'].'</a>', 'type'=>'checkbox', 'description'=>$data['Description']];
				}
			}
		}

		if($extend_handle = opendir($extend_dir)) {   
			while (($extend_file = readdir($extend_handle)) !== false) {
				if ($extend_file == '.' || $extend_file == '..' || !is_file($extend_dir.'/'.$extend_file) || !empty($wpjam_extends[$extend_file])){
					continue;
				}

				if(pathinfo($extend_file, PATHINFO_EXTENSION) != 'php') {
					continue;
				}

				$data	= get_file_data($extend_dir.'/'.$extend_file, $file_headers);

				if($data['Name']){
					$fields[$extend_file] = ['title'=>'<a href="'.$data['URI'].'" target="_blank">'.$data['Name'].'</a>', 'type'=>'checkbox', 'description'=>$data['Description']];
				}
			}   
			closedir($extend_handle);   
		}
	} 

	if(is_multisite() && !is_network_admin()){
		$sitewide_extends = get_site_option('wpjam-extends');

		unset($sitewide_extends['plugin_page']);

		if($sitewide_extends){
			foreach ($sitewide_extends as $extend_file => $value) {
				if($value){
					unset($fields[$extend_file]);
				}
			}
		}
	}

	$summary	= is_network_admin() ? '在管理网络激活将整个站点都会激活！' : '';
	$ajax		= false;
	$sanitize_callback	= '';

	return compact('summary', 'fields', 'ajax', 'sanitize_callback');
});

if(isset($_GET['reset'])){
	delete_option('wpjam-extends');
}

wp_add_inline_style('list-tables', "\n".'.form-table th a{text-decoration:none;}'."\n");

