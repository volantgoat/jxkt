<?php

if(is_paged()){
    include(get_template_directory().'/404.php');
    exit;
}

get_header();
get_template_part( 'template-parts/banner');?>
	<div id="a1portalSkin_mainArea" class="full-screen clearfix">
		<?php get_template_part( 'template-parts/index-modular');?>
	</div>
<?php get_footer();?>