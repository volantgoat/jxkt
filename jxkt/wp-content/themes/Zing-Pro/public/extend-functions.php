<?php

//SMTP邮箱设置
if ( dahuzi('smtp_switcher') ) {
    function dahuzi_mail_smtp($phpmailer) {
        $phpmailer->From = dahuzi('dahuzi_email'); //发件人地址
        $phpmailer->FromName = dahuzi('dahuzi_mailname'); //发件人昵称
        $phpmailer->Host = dahuzi('dahuzi_mailsmtp'); //SMTP服务器地址
        $phpmailer->Port = dahuzi('dahuzi_mailport'); //SMTP邮件发送端口
        if (dahuzi('dahuzi_smtpssl')) {
            $phpmailer->SMTPSecure = 'ssl';
        } else {
            $phpmailer->SMTPSecure = '';
        } //SMTP加密方式(SSL/TLS)没有为空即可
        $phpmailer->Username = dahuzi('dahuzi_mailuser'); //邮箱帐号
        $phpmailer->Password = dahuzi('dahuzi_mailpass'); //邮箱密码
        $phpmailer->IsSMTP();
        $phpmailer->SMTPAuth = true; //启用SMTPAuth服务

    }
    add_action('phpmailer_init', 'dahuzi_mail_smtp');
}

//CDN加速储存
if ( !is_admin() && dahuzi('cdn_type') != '0' ) {
    add_action('wp_loaded', 'xintheme_ob_start');
    function xintheme_ob_start() {
        ob_start('xintheme_cdn_replace');
    }
    function xintheme_cdn_replace($html) {
        $local_host = home_url(); //博客域名
        $cdn_host = dahuzi('cdn_url'); //CDN域名
        $cdn_exts = dahuzi('cdn_file_format'); //扩展名（使用|分隔）
        $cdn_dirs = dahuzi('cdn_mirror_folder'); //目录（使用|分隔）
        $cdn_dirs = str_replace('-', '\-', $cdn_dirs);
        if ($cdn_dirs) {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/((' . $cdn_dirs . ')\/[^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $cdn_host . '/$1$4', $html);
        } else {
            $regex = '/' . str_replace('/', '\/', $local_host) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
            $html = preg_replace($regex, $cdn_host . '/$1$3', $html);
        }
        return $html;
    }
}
//自动替换媒体库图片的域名
if ( is_admin() && dahuzi('cdn_type') != '0' ) {
    function xintheme_attachment_replace($text) {
        $replace = array(
            '' .home_url(). '' => '' .dahuzi('cdn_url'). ''
        );
        $text = str_replace(array_keys($replace) , $replace, $text);
        return $text;
    }
    add_filter('wp_get_attachment_url', 'xintheme_attachment_replace');
}

//链接转换
if( dahuzi('xintheme_simple_urls') ) :
    include_once TEMPLATEPATH.'/public/extend/simple-urls/simple-urls.php';
endif;

//数据库清理
if( dahuzi('xintheme_wp-clean-up') ) :
    include_once TEMPLATEPATH.'/public/extend/wp-clean-up/wp-clean-up.php';
endif;

//站点地图
if( dahuzi('xintheme_sitemap') ) :
    include_once TEMPLATEPATH.'/public/extend/Sitemap/sitemap.php';
endif;

//自定义文章排序
if( dahuzi('dahuzi_custom_sort') ) :
	include_once TEMPLATEPATH.'/public/core/dahuzi_post_sort/custom_sort.php';
endif;