<?php
$response	= WPJAM_Verify::get_qrcode();

if(is_wp_error($response)){
	wpjam_register_page_action('verify_wpjam', $response);
}else{
	$summary	= '
	<p><strong>通过验证才能使用 WPJAM Basic 的扩展功能。 </strong></p>
	<p>1. 使用微信扫描下面的二维码获取验证码。<br />
	2. 将获取验证码输入提交即可！<br />
	3. 如果验证不通过，请使用 Chrome 浏览器验证，并在验证之前清理浏览器缓存。</p>
	';

	$qrcode = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$response['ticket'];

	wpjam_register_page_action('verify_wpjam', [
		'submit_text'	=> '验证',
		'callback'		=> ['WPJAM_Verify', 'ajax_verify'],
		'response'		=> 'redirect',
		'fields'		=> [
			'summary'	=> ['title'=>'',		'type'=>'view',		'value'=>$summary],
			'qrcode'	=> ['title'=>'二维码',	'type'=>'view',		'value'=>'<img src="'.$qrcode.'" style="max-width:250px;" />'],
			'code'		=> ['title'=>'验证码',	'type'=>'number',	'class'=>'all-options',	'description'=>'验证码10分钟内有效！'],
			'scene'		=> ['title'=>'scene',	'type'=>'hidden',	'value'=>$response['scene']]
		]
	]);

	wp_add_inline_style('list-tables', "\n".'.form-table th{width: 100px;}');
}

function wpjam_verify_page(){
	$page_form	= wpjam_get_page_form('verify_wpjam');

	echo is_wp_error($page_form) ? '<div class="notice notice-error"><p>'.$page_form->get_error_message().'</p></div>' : $page_form;
}