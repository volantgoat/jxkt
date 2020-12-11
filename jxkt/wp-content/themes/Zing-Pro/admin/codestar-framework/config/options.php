<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'xintheme_optimize';

//
// Create options
//
CSF::createOptions( $prefix, array(
  'menu_title' => '网站优化',
  'menu_slug'  => 'xintheme-optimize',
  'menu_icon'  => 'dashicons-wordpress',
) );

//
// SEO优化
//
CSF::createSection( $prefix, array(
  'id'    => 'seo',
  'title' => 'SEO优化',
  //'icon'  => 'fa fa-tint',
) );

//
// SEO优化：SEO设置
//
CSF::createSection( $prefix, array(
  'parent'      => 'seo',
  'title'       => '网站SEO设置',
  'fields'      => array(

    array(
      'id'    => 'connector',
      'type'  => 'text',
      'title' => '标题分隔符',
      'default'=> '-',
      'desc'  => '一般设置为 - 或 _ 不要留空格',
      'attributes'   => array('style'=> 'width: 10%;'),
    ),

    array(
      'id'    => 'hometitle',
      'type'  => 'text',
      'title' => '首页标题',
      'desc'  => '自定义首页标题，留空则自动调用「后台-设置-常规」中的“站点标题+副标题”的内容',
      'attributes'   => array('style'=> 'width: 100%;'),
    ),

    array(
      'id'    => 'home_description',
      'type'  => 'textarea',
      'title' => '首页描述',
      'desc'  => '一段简单的描述文字',
      'attributes'   => array('style'=> 'width: 100%;'),
    ),

    array(
      'id'    => 'home_keywords',
      'type'  => 'textarea',
      'title' => '首页关键词',
      'desc'  => '多个关键词之间用英文逗号隔开',
      'attributes'   => array('style'=> 'width: 100%;'),
    ),

  )
) );

//
// SEO优化：主动推送
//
CSF::createSection( $prefix, array(
  'parent'      => 'seo',
  'title'       => '百度-普通收录',
  'fields'      => array(

    //主动推送
    array(
      'id'    => 'XinTheme_Baidu_Submit',
      'type'  => 'switcher',
      'title' => '',
      'label' => '百度站长平台-普通收录（API提交）',
    ),
    array(
      'id'    => 'Baidu_Submit_url',
      'type'  => 'text',
      'title' => '网站域名',
      'after'      => '<p class="cs-text-muted">记得填写“http://”或者“https://”</p>',
      'attributes'   => array('style'=> 'width: 100%;'),
      'dependency'   => array( 'XinTheme_Baidu_Submit', '==', true ),
    ),
    array(
      'id'    => 'Baidu_Submit_token',
      'type'  => 'text',
      'title' => '准入密钥（token）',
      'attributes'   => array('style'=> 'width: 100%;'),
      'dependency'   => array( 'XinTheme_Baidu_Submit', '==', true ),
    ),


  )
) );

//
// SEO优化：百度移动专区
//
CSF::createSection( $prefix, array(
  'parent'      => 'seo',
  'title'       => '百度-快速收录',
  'fields'      => array(

    //熊账号
    array(
      'id'    => 'xiongzhanghao',
      'type'  => 'switcher',
      'title' => '',
      'label' => '百度站长平台-快速收录（API提交）',
    ),
    array(
      'id'    => 'xzh_appid',
      'type'  => 'text',
      'title' => '网站域名',
      'after'      => '<p class="cs-text-muted">记得填写“http://”或者“https://”</p>',
      'attributes'   => array('style'=> 'width: 100%;'),
      'dependency'   => array( 'xiongzhanghao', '==', true ),
    ),
    array(
      'id'    => 'xzh_post_token',
      'type'  => 'text',
      'title' => '准入密钥（token）',
      'attributes'   => array('style'=> 'width: 100%;'),
      'dependency'   => array( 'xiongzhanghao', '==', true ),
    ),


  )
) );

//
// 在线留言
//
CSF::createSection( $prefix, array(
  'title'  => '在线留言',
  'fields' => array(

		array(
		    'type'      => 'subheading',
		    'content'   => '老用户注意一下，使用此功能前，请重新启用一下主题，就是先切换到其他任何一个主题，然后在启用Zing-Pro主题就可以了，新用户可以无视。',
		),

        array(
            'id'        => 'contact_timthumb',
            'type'      => 'media',
            'title'     => '特色图片',
            'add_title' => '上传图片',
            'desc'  	=> '建议尺寸：560×395 px',
        ),
	    array(
			'id'		=> 'mail_contact',
			'type'		=> 'text',
			'title'		=> '通知邮箱',
			'desc'  	=> '当有访客提交留言信息后，通过邮件发送到这个指定邮箱账号',
	    ),
	    array(
			'id'		=> 'post_contact',
			'type'		=> 'switcher',
			'title'		=> '',
			'label'		=> '文章内容底部，显示留言表单',
	    ),


  )
) );

//
// 其他设置
//
CSF::createSection( $prefix, array(
  'title'  => '其他设置',
  'fields' => array(

        array(
            'id'        => 'default_timthumb',
            'type'      => 'media',
            'title'     => '文章默认缩略图',
            'add_title' => '上传图片',
        ),
        
        array(
            'id'        => 'dahuzi_404',
            'type'      => 'radio',
            'title'     => '404 页面',
            'options'   => array(
                'default'   => '默认404页面',
                'tencent'   => '腾讯公益404页面',
            ),
            'default'   => 'default',
        ),

  )
) );

//
// WP优化
//
CSF::createSection( $prefix, array(
  'title'  => 'WP优化<span style="color:#ffffff;background:#43A047;padding:1px 3px;border-radius: 3px;font-size:12px;margin-left:5px">新</span>',
  'fields' => array(

    array(
      'id'    => 'dahuzi_no_admin_comments',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '隐藏后台【评论】菜单，企业站很多用不到评论，不需要的可以隐藏掉<span style="color:#ffffff;background:#dd3544;padding:1px 3px;border-radius: 3px;font-size:12px;margin-left:5px">新</span>',
    ),
    array(
      'id'    => 'dahuzi_links_nofollow',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '给「链接」增加Nofollow属性选项，这个属性基本上都知道吧？不懂的还是看下百度百科吧：<a target="_blank" href="https://baike.baidu.com/item/Nofollow/2410595?fr=aladdin">点击查看</a>',
    ),
    array(
      'id'    => 'dahuzi_instantpage',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '瞬间预加载（Instant Page）',
    ),
    array(
      'id'    => 'xintheme_moveposttotrash',
      'type'  => 'switcher',
      'title' => '',
      'label' => '后台Ajax删除文章（https://www.xintheme.com/wpjiaocheng/93394.html）',
    ),
    array(
      'id'    => 'no_category',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '去除固定连接中的「category」标志',
    ),
    array(
      'id'    => 'no_zifenlei',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '去除固定链接中的子分类，去掉前：www.xxx.com/fenlei/zifenlei/123.html，去掉后：www.xxx.com/fenlei/123.html',
    ),
    array(
      'id'    => 'xintheme_pubMissedPosts',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '修复WordPress文章定时发布失败的问题',
    ),
    array(
      'id'    => 'xintheme_remove_script_version',
      'type'  => 'switcher',
      'title' => '',
      'label'  => '去除前端css和js版本号，一定程度上会加强些网站安全',
    ),
    array(
      'id'    => 'xintheme_pingback',
      'type'  => 'switcher',
      'title' => '',
      'label' => '关闭 XML-RPC 和 pingback 端口',
    ),
    array(
      'id'    => 'xintheme_v2ex',
      'type'  => 'switcher',
      'title' => '',
      'label' => '使用v2ex镜像avatar头像，加快加载速度',
    ),
    array(
      'id'    => 'no_admin_bar',
      'type'  => 'switcher',
      'title' => '',
      'label' => '去除wordpress前台顶部工具条',
    ),
    array(
      'id'    => 'xintheme_wp_head',
      'type'  => 'switcher',
      'title' => '',
      'label' => '移除顶部多余信息',
    ),
    array(
      'id'    => 'xintheme_feed',
      'type'  => 'switcher',
      'title' => '',
      'label' => '禁止FEED，防采集',
    ),
    array(
      'id'    => 'xintheme_language',
      'type'  => 'switcher',
      'title' => '',
      'label' => '禁止前台加载语言包',
    ),
    array(
      'id'    => 'redirect_search',
      'type'  => 'switcher',
      'title' => '',
      'label' => '修改搜索结果的链接，修改前：域名/?s=搜索词，修改后：域名/search/搜索词',
    ),
    array(
      'id'    => 'xintheme_option_thumbnail',
      'type'  => 'switcher',
      'title' => '',
      'label' => '彻底关闭WordPress生成默认尺寸的缩略图',
    ),
    array(
      'id'    => 'xintheme_article',
      'type'  => 'switcher',
      'title' => '',
      'label' => '登录后台后默认跳转到文章列表',
    ),
    array(
      'id'    => 'xintheme_privacy',
      'type'  => 'switcher',
      'title' => '',
      'label' => '彻底删除后台隐私相关设置',
    ),
    array(
      'id'    => 'xintheme_delete_post_attachments',
      'type'  => 'switcher',
      'title' => '',
      'label' => '删除文章时删除图片附件',
    ),
    array(
      'id'    => 'xintheme_upload_img_rename',
      'type'  => 'switcher',
      'title' => '',
      'label' => '上传图片使用日期重命名',
    ),
    array(
      'id'    => 'xintheme_no_gutenberg',
      'type'  => 'switcher',
      'title' => '',
      'label' => '禁用古腾堡编辑器',
    ),
    array(
      'id'    => 'xintheme_post_nofollow',
      'type'  => 'switcher',
      'title' => '',
      'label' => '文章外链自动添加nofollow标签',
    ),


  )
) );

//
// 扩展功能
//
CSF::createSection( $prefix, array(
  'id'    => 'extend_fields',
  'title' => '扩展功能',
  //'icon'  => 'fa fa-tint',
) );


//
// 扩展功能：自定义文章排序、分类目录排序
//

$options_taxonomies = array();
$options_taxonomies_obj = get_taxonomies(array( 'show_ui' => true ), 'objects');
foreach ( $options_taxonomies_obj as $taxonomy ) {

	if( $taxonomy->name == 'category' ){
		//$taxonomy_name = '分类目录';
		$options_taxonomies['category'] = '分类目录';
	}
	//if( $taxonomy->name == 'post_tag' ){
		//$taxonomy_name = '标签「暂不支持排序」';
	//}
	if( $taxonomy->name == 'link_category' ){
		//$taxonomy_name = '链接分类目录';
		$options_taxonomies['link_category'] = '链接分类目录';
	}
	//$options_taxonomies[$taxonomy->name] = $taxonomy_name; //输出所有自定义分类法

}

CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => '自定义文章排序',
  'description' => '自定义文章排序，功能介绍请点击：<a href="https://www.xintheme.com/wpjiaocheng/93513.html" target="_blank">https://www.xintheme.com/wpjiaocheng/93513.html</a>',
  'fields'      => array(

    array(
      'id'    => 'dahuzi_custom_sort',
      'type'  => 'switcher',
      'title' => '启用自定义排序',
    ),
    array(
      'id'          => 'dahuzi_post_type',
      'type'        => 'checkbox',
      'title'       => '启用文章排序',
      'options'     => 'post_types',
      'dependency'  => array( 'dahuzi_custom_sort', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_cat_type',
      'type'        => 'checkbox',
      'title'       => '启用分类排序',
      'options'     => $options_taxonomies,
      'dependency'  => array( 'dahuzi_custom_sort', '==', true ),
    ),

    )
) );


//
// 扩展功能：SMTP邮箱设置
//
CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => 'SMTP邮箱设置',
  'fields'      => array(

    array(
      'id'    => 'smtp_switcher',
      'type'  => 'switcher',
      'title' => '启用SMTP服务',
    ),

    array(
      'id'          => 'dahuzi_email',
      'type'        => 'text',
      'title'       => '发件人邮箱',
      'desc'        => '请输入您的邮箱地址',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_mailname',
      'type'        => 'text',
      'title'       => '发件人昵称',
      'desc'        => '请输入发件人昵称',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_mailsmtp',
      'type'        => 'text',
      'title'       => 'SMTP服务器地址',
      'desc'        => '请输入您邮箱的SMTP服务器地址',
      'default'     => 'smtp.qq.com',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_smtpssl',
      'type'        => 'switcher',
      'title'       => 'SSL安全连接',
      'default'     => true,
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_mailport',
      'type'        => 'number',
      'title'       => 'SMTP服务器端口',
      'default'     => '465',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_mailuser',
      'type'        => 'text',
      'title'       => '邮箱帐号',
      'desc'        => '请输入您的邮箱地址，例如：670088886@qq.com',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

    array(
      'id'          => 'dahuzi_mailpass',
      'type'        => 'text',
      'title'       => '邮箱认证密码',
      'desc'        => '如果使用QQ邮箱，这里输入的不是QQ密码，请前往QQ邮箱 - 设置 - 账户中生成授权码',
      'attributes'  => array('style'=> 'width: 50%;'),
      'dependency'  => array( 'smtp_switcher', '==', true ),
    ),

  )
) );

//
// 扩展功能：对象储存
//
CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => '对象储存',
  'fields'      => array(

    array(
			'id'    		=> 'cdn_type',
			'type'  		=> 'select',
			'title' 		=> '选择云储存',
			'options'		=> array(
				'0' 		  => '选择云储存',
				'qiniu'   => '七牛云储存',
				'alioss'  => '阿里云OSS'
			),
    ),
    array(
      'id'        => 'cdn_url',
      'type'			=> 'text',
      'title'			=> '加速域名',
			'after'			=> '<p class="cs-text-muted">不要忘记了“http(s)://”</p>',
			'attributes'=> array('style'=> 'width: 50%;'),
			'dependency'=> array( 'cdn_type', 'any', 'qiniu,alioss' )
    ),
    array(
      'id'        => 'cdn_file_format',
      'type'			=> 'text',
      'title'			=> '镜像文件格式',
			'default'		=> 'png|jpg|jpeg|gif|ico|7z|zip|rar|pdf|ppt|wmv|mp4|avi|mp3|txt',
			'after'   	=> '<p class="cs-text-muted">在输入框内添加准备镜像的文件格式，比如png|jpg|jpeg|gif|ico|html|7z|zip|rar|pdf|ppt|wmv|mp4|avi|mp3|txt（使用|分隔）</p>',
			'attributes'=> array('style'=> 'width: 50%;'),
			'dependency'=> array( 'cdn_type', 'any', 'qiniu,alioss' )
    ),
    array(
      'id'        => 'cdn_mirror_folder',
      'type'			=> 'text',
      'title'			=> '镜像文件夹',
			'default'		=> 'wp-content|wp-includes',
			'after'			=> '<p class="cs-text-muted">在输入框内添加准备镜像的文件夹，比如wp-content|wp-includes（使用|分隔）</p>',
			'attributes'=> array('style'=> 'width: 50%;'),
			'dependency'=> array( 'cdn_type', 'any', 'qiniu,alioss' )
    ),

  )
) );

//
// 扩展功能：链接转换
//
CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => '链接转换',
  'fields'      => array(

    array(
      'id'      => 'xintheme_simple_urls',
      'type'    => 'switcher',
      'title'   => '外链转内链',
      'desc'    => '开启后刷新一下页面即可在菜单栏显示按钮',
      'help'    => '集成Simple Urls外链转内链插件，开启即可使用。',
      'default' => false
    ),


  )
) );

//
// 扩展功能：数据库优化清理
//
CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => '数据库优化清理',
  'fields'      => array(

    array(
      'id'      => 'xintheme_wp-clean-up',
      'type'    => 'switcher',
      'title'   => '数据库优化清理',
      'desc'    => '开启后刷新一下页面，在外观 - 数据库清理 中进行优化',
      'help'    => '集成wp-clean-up插件，WordPress数据库优化，它包含删除冗余数据和数据库优化两大功能，操作界面十分简洁易于理解。',
      'default' => false
    ),


  )
) );

//
// 扩展功能：站点地图（Sitemap）
//
CSF::createSection( $prefix, array(
  'parent'      => 'extend_fields',
  'title'       => '站点地图（Sitemap）',
  'fields'      => array(

    array(
      'id'      => 'xintheme_sitemap',
      'type'    => 'switcher',
      'title'   => '站点地图',
      'desc'    => '开启后刷新一下页面，在外观 - 站点地图 中进行设置',
      'help'    => '自动生成xml文件，遵循Sitemap协议，用于指引搜索引擎快速、全面的抓取或更新网站上内容。兼容百度、google、360等主流搜索引擎。',
      'default' => false
    ),


  )
) );

//
// 添加代码
//
CSF::createSection( $prefix, array(
  'title'  => '添加代码',
  'fields' => array(

    array(
      'id'        => 'code_head',
      'type'      => 'code_editor',
      'title'     => '添加到头部',
      'desc'      => '支持html、css、js，js请添加&lt;script&gt;闭合标签，css请添加&lt;style&gt;闭合标签',
      'settings'  => array(
        'theme'   => 'shadowfox',
        'mode'    => 'htmlmixed',
      ),
    ),
    array(
      'id'        => 'code_foot',
      'type'      => 'code_editor',
      'title'     => '添加到页脚',
      'desc'      => '支持html、css、js，js请添加&lt;script&gt;闭合标签，css请添加&lt;style&gt;闭合标签',
      'settings'  => array(
        'theme'   => 'shadowfox',
        'mode'    => 'htmlmixed',
      ),
    ),

  )
) );

//
// 主题更新
//
CSF::createSection( $prefix, array(
  'title'  => '禁用主题更新',
  'fields' => array(

    array(
      'id'      => 'dahuzi_theme_update',
      'type'    => 'switcher',
      'title'   => '禁用主题更新',
      'desc'    => '开启后，后台将不再显示主题更新提示，如果你以后都不需要更新主题了，则可以开启',
      'default' => false
    ),

  )
) );

add_action('admin_head',function(){ ?>

<style type="text/css">
  .csf-content{min-height:74vh}
</style>

<?php });

//授权+在线更新
include TEMPLATEPATH.'/admin/codestar-framework/fields/updater/theme-updater.php';
