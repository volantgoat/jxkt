<?php
	$index_modular = xintheme('index_modular');
	if(is_array($index_modular)){
		foreach($index_modular as $id):
		$modular_type=$id['modular_type'];
			switch ($modular_type){
				case	'1':	get_template_part( 'template-parts/modular/1');break;
				case	'2':	get_template_part( 'template-parts/modular/2');break;
				case	'3':	get_template_part( 'template-parts/modular/3');break;
				case	'4':	get_template_part( 'template-parts/modular/4');break;
				case	'5':	get_template_part( 'template-parts/modular/5');break;
				case	'6':	get_template_part( 'template-parts/modular/6');break;
				case	'7':	get_template_part( 'template-parts/modular/7');break;
				case	'8':	get_template_part( 'template-parts/modular/8');break;
				case	'9':	get_template_part( 'template-parts/modular/9');break;
				case	'10':	get_template_part( 'template-parts/modular/10');break;
				case	'11':	get_template_part( 'template-parts/modular/11');break;
				case	'12':	get_template_part( 'template-parts/modular/12');break;
				case	'13':	get_template_part( 'template-parts/modular/13');break;
				case	'14':	get_template_part( 'template-parts/modular/14');break;
			}
		endforeach;
	}else{
		echo "<div style='text-align:center;line-height:80vh;font-size:22px;font-weight:600'><a href='".wp_customize_url()."'>请到「后台 - 外观 - 自定义」中添加首页模块</a></div>";
	}
?>