<?php
$category = get_the_category();
if($category[0]){
    $catid = $category[0]->term_id;
}
$category_data = get_term_meta( $catid, '_prefix_taxonomy_options', true );
$category_layout = isset($category_data['cat_layout']) ?$category_data['cat_layout'] : '';

$post_extend = get_post_meta( get_the_ID(), 'extend_info', true );
$post_layout = isset($post_extend['post_layout']) ?$post_extend['post_layout'] : '';

get_header();

if(xintheme('cat_type_single')){

	if( $category_layout == 'grid' || $category_layout == 'grid-no-sidebar' || $post_layout == 'grid' ){
		include( 'single/single-grid.php' );
	}elseif($post_layout == 'news'){
		get_template_part( 'single/single-news' );
	}else{
		get_template_part( 'single/single-news' );
	}

}else{

	if( $post_layout == 'grid' ){
		include( 'single/single-grid.php' );
	}elseif($post_layout == 'news'){
		get_template_part( 'single/single-news' );
	}else{
		get_template_part( 'single/single-news' );
	}

}

get_footer();?>