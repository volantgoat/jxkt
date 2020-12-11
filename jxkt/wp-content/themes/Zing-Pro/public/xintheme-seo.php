<?php
/**
 * @Author: 大胡子
 * @Email:  dahuzi@xintheme.com
 * @Link:   www.dahuzi.me
 * @Date:   2020-03-03 01:11:07
 * @Last Modified by:   dahuzi
 * @Last Modified time: 2020-07-27 16:53:09
 */

add_action('wp_head', 'dahuzi_the_head', 0);
function dahuzi_the_head() {
    dahuzi_seo_keywords();
    dahuzi_seo_description();
}

// 标题分隔符
function _get_delimiter(){
    return dahuzi('connector') ? dahuzi('connector') : '-';
}

function _get_tax_meta($id=0, $field=''){
    $ops = get_option( "_taxonomy_meta_$id" );

    if( empty($ops) ){
        return '';
    }

    if( empty($field) ){
        return $ops;
    }

    return isset($ops[$field]) ? $ops[$field] : '';
}

// 网站标题
function dahuzi_seo_title() {
    global $new_title;
    if( $new_title ) return $new_title;

    global $paged;

    $html = '';
    $t = trim(wp_title('', false));

    if ($t) {
        $html .= $t . _get_delimiter();
    }

    $html .= get_bloginfo('name');

    if (is_home()) {
        if(dahuzi('hometitle')){
            $html = dahuzi('hometitle');
        }else{
            if( get_option('blogdescription') ){
                $html .= _get_delimiter() . get_option('blogdescription');
            }
        }
    }

    if( is_category() ){
        global $wp_query; 
        $cat_ID = get_query_var('cat');
        $category = get_term_meta( $cat_ID, '_prefix_taxonomy_options', true );
        $seo_str = isset($category['seo_title']) ?$category['seo_title'] : '';
        $cat_tit = ($seo_str) ? $seo_str : _get_tax_meta($cat_ID, 'title');
        if( $cat_tit ){
            $html = $cat_tit;
        }
    }

    if( (is_single() || is_page()) ){
        global $post;
        $post_ID = $post->ID;
        $post_metabox = get_post_meta($post_ID, 'extend_info', true );
        if( is_page() ){
        	$post_metabox = get_post_meta($post->ID, 'page_seo', true );
        }
        $post_seo = isset($post_metabox['seo_title']) ?$post_metabox['seo_title'] : '';
        $seo_title = trim($post_seo);
        if($seo_title) $html = $seo_title;
    }

    if ($paged > 1) {
        $html .= _get_delimiter() . '第' . $paged . '页';
    }

    return $html;
}

// 网站关键词
function dahuzi_seo_keywords() {
    global $new_keywords;
    if( $new_keywords ) {
        echo "<meta name=\"keywords\" content=\"{$new_keywords}\">\n";
        return;
    }

    global $s, $post;
    $keywords = '';
    if (is_singular()) {
        if (get_the_tags($post->ID)) {
            foreach (get_the_tags($post->ID) as $tag) {
                $keywords .= $tag->name . ', ';
            }
        }
        foreach (get_the_category($post->ID) as $category) {
            $keywords .= $category->cat_name . ', ';
        }
        $keywords = substr_replace($keywords, '', -2);

        $post_metabox = get_post_meta($post->ID, 'extend_info', true );
        if( is_page() ){
        	$post_metabox = get_post_meta($post->ID, 'page_seo', true );
        }
        $post_seo = isset($post_metabox['seo_keywords']) ?$post_metabox['seo_keywords'] : '';
        $the = trim($post_seo);

        if ($the) {
            $keywords = $the;
        }
    } elseif (is_home()) {
        $keywords = dahuzi('home_keywords');
    } elseif (is_tag()) {
        $keywords = single_tag_title('', false);
    } elseif (is_category()) {

        global $wp_query; 
        $cat_ID = get_query_var('cat');
        $category = get_term_meta( $cat_ID, '_prefix_taxonomy_options', true );
        $seo_str = isset($category['seo_keywords']) ?$category['seo_keywords'] : '';
        $keywords = ($seo_str) ? $seo_str : _get_tax_meta($cat_ID, 'keywords');
        if( !$keywords ){
            $keywords = single_cat_title('', false);
        }
    
    } elseif (is_search()) {
        $keywords = esc_html($s, 1);
    } else {
        $keywords = trim(wp_title('', false));
    }
    if ($keywords) {
        echo "<meta name=\"keywords\" content=\"{$keywords}\">\n";
    }
}

// 网站描述
function dahuzi_seo_description() {
    global $new_description;
    if( $new_description ){
        echo "<meta name=\"description\" content=\"$new_description\">\n";
        return;
    }

    global $s, $post;
    $description = '';
    $blog_name = get_bloginfo('name');
    if (is_singular()) {
        if (!empty($post->post_excerpt)) {
            $text = $post->post_excerpt;
        } else {
            $text = $post->post_content;
        }
        $description = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags($text))));
        $description = mb_substr($description, 0, 200, 'utf-8');

        if (!$description) {
            $description = $blog_name . "-" . trim(wp_title('', false));
        }

        $post_metabox = get_post_meta($post->ID, 'extend_info', true );
        if( is_page() ){
        	$post_metabox = get_post_meta($post->ID, 'page_seo', true );
        }
        $post_seo = isset($post_metabox['seo_description']) ?$post_metabox['seo_description'] : '';
        $the = trim($post_seo);

        if ($the) {
            $description = $the;
        }
        
    } elseif (is_home()) {
        $description = dahuzi('home_description');
    } elseif (is_tag()) {
        $description = trim(strip_tags(tag_description()));
    } elseif (is_category()) {

        global $wp_query; 
        $cat_ID = get_query_var('cat');
        $category = get_term_meta( $cat_ID, '_prefix_taxonomy_options', true );
        $seo_str = isset($category['seo_description']) ?$category['seo_description'] : '';
        $description = ($seo_str) ? $seo_str : _get_tax_meta($cat_ID, 'description');
        if( !$description ){
            $description = trim(strip_tags(category_description()));
        }

    } elseif (is_archive()) {
        $description = $blog_name . "'" . trim(wp_title('', false)) . "'";
    } elseif (is_search()) {
        $description = $blog_name . ": '" . esc_html($s, 1) . "' 的搜索結果";
    } else {
        $description = $blog_name . "'" . trim(wp_title('', false)) . "'";
    }
    
    echo "<meta name=\"description\" content=\"$description\">\n";
}


// 熊掌号 新文章发布时实时推送（天级收录）
add_action('publish_post', 'tb_xzh_post_to_baidu');
function tb_xzh_post_to_baidu() {
    if( dahuzi('xiongzhanghao') && dahuzi('xzh_appid') && dahuzi('xzh_post_token') ){
        global $post;
        $plink = get_permalink($post->ID);

        if( !$plink || get_post_meta($post->ID, 'xzh_tui_back', true) ){
            return false;
        }

        $urls = array();
        $urls[] = $plink;
        $api = 'http://data.zz.baidu.com/urls?site='. dahuzi('xzh_appid') .'&token='. dahuzi('xzh_post_token') .'&type=daily';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $result = json_decode($result);
        $result_text = '成功';
        if( $result->error ){
            $result_text = '失败 '.$result->message;
        }
        update_post_meta($post->ID, 'xzh_tui_back', $result_text);
    
    }
}

// WordPress发布文章主动推送到百度，加快收录保护原创
if(!function_exists('XinTheme_Baidu_Submit') && function_exists('curl_init')) {
    function XinTheme_Baidu_Submit($post_ID) {
    if( dahuzi('XinTheme_Baidu_Submit') && dahuzi('Baidu_Submit_url') && dahuzi('Baidu_Submit_token') ){
        $WEB_SITE = dahuzi('Baidu_Submit_url'); //这里换成你的域名
        $WEB_TOKEN = dahuzi('Baidu_Submit_token'); //这里换成你的网站的百度主动推送的token值
        //已成功推送的文章不再推送
        if(get_post_meta($post_ID,'Baidusubmit',true) == 1) return;
        $url = get_permalink($post_ID);
        $api = 'http://data.zz.baidu.com/urls?site='.$WEB_SITE.'&token='.$WEB_TOKEN;
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $url,
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = json_decode(curl_exec($ch),true);
        
        //如果推送成功则在文章新增自定义栏目Baidusubmit，值为1
        if (array_key_exists('success',$result)) {
            add_post_meta($post_ID, 'Baidusubmit', 1, true);
        }
    }
    }
    add_action('publish_post', 'XinTheme_Baidu_Submit', 0);
}
