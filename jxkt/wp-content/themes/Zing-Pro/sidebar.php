<aside class="sidebar sidebar-page">
    <?php
    if (is_single()){
        $category = get_the_category();
        if($category[0]){
            $cat = $category[0]->term_id;
        }
    }
    $category_data = get_term_meta( $cat, '_prefix_taxonomy_options', true );
    $category_type = isset($category_data['cat_layout']) ?$category_data['cat_layout'] : '';

    $post_extend = get_post_meta( get_the_ID(), 'extend_info', true );
    $post_layout = isset($post_extend['post_layout']) ?$post_extend['post_layout'] : '';

    if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_right')) : endif;

    if (is_single()){
        if( $post_layout == 'grid' || $category_type == 'grid' || $category_type == 'grid-no-sidebar' ){
            if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_produc')) : endif;
        }else{
            if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_post')) : endif;
        }
    }

    else if (is_page()){
        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_page')) : endif;
    }

    else if (is_home()){
        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_sidebar')) : endif;
    }

    else if($category_type == 'grid'){
        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_product')) : endif;
    }

    else if($category_type == 'news' || $category_type == 'news-img'){
        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_news')) : endif;
    }

    else {
        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_other')) : endif;
    }
    ?>
</aside>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery('.sidebar').theiaStickySidebar({
      // Settings
      additionalMarginTop: 30
    });
  });
</script>