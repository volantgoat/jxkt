<?php

/** --------------------------------------------------------------------------------- *
 *  codestar-framework 相关
 *  --------------------------------------------------------------------------------- */
include TEMPLATEPATH.'/admin/codestar-framework/codestar-framework.php'; // codestar-framework
include TEMPLATEPATH.'/admin/codestar-framework/config/customize.php'; // 自定义设置
include TEMPLATEPATH.'/admin/codestar-framework/config/taxonomy.php'; // 分类扩展
include TEMPLATEPATH.'/admin/codestar-framework/config/options.php'; // 通用优化设置
include TEMPLATEPATH.'/admin/codestar-framework/config/metabox.php'; // 文章扩展
include TEMPLATEPATH.'/admin/codestar-framework/config/shortcoder.php'; // 短代码配置器

if ( !function_exists('xintheme') ) {
  function xintheme( $option = '', $default = null ) {
    $options = get_option('xintheme_customize');
    return ( isset( $options[$option] ) ) ? $options[$option] : $default;
  }
}

if ( !function_exists('xintheme_img') ) {
    function xintheme_img($option = '', $default = '')
    {
        $options = get_option('xintheme_customize');
        return ( isset( $options[$option]['url'] ) ) ? $options[$option]['url'] : $default;
    }
}

if ( !function_exists('dahuzi') ) {
  function dahuzi( $option = '', $default = null ) {
    $options = get_option('xintheme_optimize');
    return ( isset( $options[$option] ) ) ? $options[$option] : $default;
  }
}
if ( !function_exists('dahuzi_img') ) {
  function dahuzi_img( $option = '', $default = null ) {
    $options = get_option('xintheme_optimize');
    return ( isset( $options[$option]['url'] ) ) ? $options[$option]['url'] : $default;
  }
}

/** --------------------------------------------------------------------------------- *
 *  核心文件
 *  --------------------------------------------------------------------------------- */
include TEMPLATEPATH.'/public/core/dahuzi-cache.php'; //
include TEMPLATEPATH.'/public/xintheme-seo.php'; //SEO设置
include TEMPLATEPATH.'/public/zing-functions.php'; //zing主题相关
include TEMPLATEPATH.'/public/basic-functions.php'; //通用函数
include TEMPLATEPATH.'/public/wp-optimize.php'; //WP优化
include TEMPLATEPATH.'/public/extend-functions.php'; //扩展功能
include TEMPLATEPATH.'/public/widget.php'; //小工具
include TEMPLATEPATH.'/public/comment.php'; //评论相关
include TEMPLATEPATH.'/public/dahuzi-contact.php'; //在线留言
include TEMPLATEPATH.'/public/core/encryption.php';

