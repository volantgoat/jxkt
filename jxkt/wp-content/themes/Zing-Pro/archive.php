<?php 
$category_data = get_term_meta( $cat, '_prefix_taxonomy_options', true );
$category_type = isset($category_data['cat_layout']) ?$category_data['cat_layout'] : '';
get_header();
if($category_type == 'grid'){
	include( 'archive/grid.php' );
}elseif($category_type == 'grid-no-sidebar'){
	get_template_part( 'archive/grid-no-sidebar' );
}elseif($category_type == 'news'){
	get_template_part( 'archive/news' );
}elseif($category_type == 'news-img'){
	get_template_part( 'archive/news-img' );
}else{
	get_template_part( 'archive/news' );
}
get_footer();?>