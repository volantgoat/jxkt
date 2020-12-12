<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'xintheme_customize';

//
// Create customize options
//
CSF::createCustomizeOptions( $prefix );

// ----------------------------------------
// 网站公告
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '网站公告',
    'priority' => 1,
    'fields'   => array(

        array(
            'id'        => 'notice_code_switch',
            'type'      => 'switcher',
            'title'     => '是否开启网站公告栏',
            'default'   => false
        ),
        array(
            'id'        => 'notice_code',
            'type'      => 'textarea',
            'title'     => '添加网站公告',
            'subtitle'  => '<p class="cs-text-muted">出现在网站最顶部，支持html标签...</p>',
            'dependency'=> array( 'notice_code_switch', '==', true )
        ),
        array(
            'id'        => 'notice_code_close',
            'type'      => 'checkbox',
            'title'     => '',
            'label'     => '显示 关闭 按钮，关闭后半个小时内不再显示',
            'default'   => false,
            'dependency'=> array( 'notice_code_switch', '==', true )
        ),
        array(
            'id'        => 'notice_no_mobile',
            'type'      => 'checkbox',
            'title'     => '',
            'label'     => '禁止手机端显示公告',
            'help'      => '开启后手机端浏览将不显示顶部公告',
            'dependency'=> array( 'notice_code_switch', '==', true ),
            'default'   => false
        ),
        array(
            'id'        => 'notice_color',
            'type'      => 'palette',
            'title'     => '选择配色',
            'options'   => array(
                'aa'     => array( '#f10', '#f10'),
                'a'     => array( '#f44336', '#f44336'),
                'b'     => array( '#e91e63', '#e91e63'),
                'c'     => array( '#9c27b0', '#9c27b0'),
                'd'     => array( '#673ab7', '#673ab7'),
                'e'     => array( '#3f51b5', '#3f51b5'),
                'f'     => array( '#2196f3', '#2196f3'),
                'g'     => array( '#03a9f4', '#03a9f4'),
                'h'     => array( '#00bcd4', '#00bcd4'),
                'i'     => array( '#009688', '#009688'),
                'j'    => array( '#4caf50', '#4caf50'),
                'k'    => array( '#ff9800', '#ff9800'),
                'l'    => array( '#ff5722', '#ff5722'),
                'm'    => array( '#fbfbfb', '#fbfbfb'),
            ),
            'dependency'=> array( 'notice_code_switch', '==', true )
        ),


    )
) );

// ----------------------------------------
// 头部设置
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '头部设置',
    'priority' => 2,
    'fields'   => array(

        array(
            'id'        => 'header_type',
            'type'      => 'image_select',
            'title'     => '选择头部样式',
            //'inline'    => true,
            //'class'     => 'horizontal',
            'options'   => array(
                '1'     => get_stylesheet_directory_uri() . '/static/images/admin/header-1.png',
                '2'     => get_stylesheet_directory_uri() . '/static/images/admin/header-2.png',
            ),
            'default'   => '1',
        ),
        array(
            'id'        => 'logo',
            'type'      => 'media',
            'title'     => '网站 LOGO',
            'help'      => '建议尺寸：224×80 px',
            'add_title' => '上传 LOGO',
            'dependency'=> array( 'header_type', 'any', '1' )
        ),
        array(
            'id'        => 'header2_logo',
            'type'      => 'media',
            'title'     => '网站 LOGO',
            'help'      => '建议尺寸：234×58 px',
            'add_title' => '上传 LOGO',
            'dependency'=> array( 'header_type', 'any', '2' )
        ),

        array(
            'id'              => 'header2_contact',
            'type'            => 'group',
            'title'           => '',
            'button_title'    => '添加联系信息',
            'accordion_title' => '添加联系信息',
            'fields'          => array(
                array(
                    'id'        => 'header2_contact_title',
                    'type'      => 'text',
                    'title'     => '标题',
                ),
                array(
                    'id'        => 'header2_contact_describe',
                    'type'      => 'text',
                    'title'     => '描述',
                ),
                array(
                    'id'            => 'footer2_contact_icon',
                    'type'          => 'icon',
                    'title'         => '选择图标',
                ),
            ),
            'dependency'=> array( 'header_type', 'any', '2' )
        ),

        array(
            'id'        => 'logo_mobile',
            'type'      => 'media',
            'title'     => '手机端 LOGO',
            'help'      => '建议尺寸：250×71 px',
            'add_title' => '上传手机端 LOGO',
        ),
        array(
            'id'        => 'favicon',
            'type'      => 'media',
            'title'     => 'Favicon 图标',
            'help'      => '建议尺寸：50×50 px',
            'add_title' => '上传 Favicon',
        ),
        array(
            'id'        => 'search_header',
            'type'      => 'switcher',
            'title'     => '搜索按钮',
            'default'   => false
        ),

    )
) );


//
// 首页模块
//
CSF::createSection( $prefix, array(
  'id'       => 'home_modular',
  'title'    => '首页模块',
  'priority' => 3,
) );

//
// 幻灯片设置
//
CSF::createSection( $prefix, array(
    'parent'   => 'home_modular',
    'title'    => '幻灯片设置',
    //'priority' => 3,
    'fields'   => array(

        array(
            'id'              => 'banner',
            'type'            => 'group',
            'title'           => '首页幻灯片设置',
            'button_title'    => '添加幻灯片',
            'accordion_title' => '添加幻灯片',
            'fields'          => array(
                array(
                    'id'                => 'banner_alt',
                    'type'              => 'text',
                    'title'             => '图片 Alt',
                ),
                array(
                    'id'                => 'banner_img',
                    'type'              => 'media',
                    'title'             => '上传图片(电脑端)',
                    'help'              => '<p class="cs-text-muted">建议尺寸 1920×700 px</p>',
                    'settings'          => array(
                        'button_title'  => '上传图片(电脑端)',
                        'frame_title'   => '选择图片(电脑端)',
                        'insert_title'  => '插入图片(电脑端)',
                    ),
                ),
                array(
                    'id'                => 'banner_img_mobile',
                    'type'              => 'media',
                    'title'             => '上传图片(手机端)',
                    'help'              => '<p class="cs-text-muted">建议尺寸 750×580 px，不上传则默认显示电脑端的图片</p>',
                    'settings'          => array(
                        'button_title'  => '上传图片(手机端)',
                        'frame_title'   => '选择图片(手机端)',
                        'insert_title'  => '插入图片(手机端)',
                    ),
                ),
                array(
                    'id'                => 'banner_url',
                    'type'              => 'text',
                    'title'             => '跳转链接',
                    'subtitle'          => '链接带上 http:// 或者 https://',
                    'attributes'        => array('style'=> 'width: 100%;'),
                ),
                array(
                    'id'                => 'banner_blank',
                    'type'              => 'checkbox',
                    'title'             => '',
                    'label'             => '新窗口打开',
                    'default'           => false
                ),
                array(
                    'id'                => 'banner_nofollow',
                    'type'              => 'checkbox',
                    'title'             => '',
                    'label'             => 'Nofollow',
                    'default'           => false
                ),

            )
        ),

    ),
) );

//
// 添加首页模块
//
CSF::createSection( $prefix, array(
    'parent'   => 'home_modular',
    'title'    => '首页模块',
    //'priority' => 3,
    'fields'   => array(

        array(
            'id'      => 'data_animate',
            'type'    => 'switcher',
            'title'   => '开启模块加载动画',
            'default' => false
        ),

        // 模块类型
        array(
            'id'                => 'index_modular',
            'type'              => 'group',
            'title'             => '添加首页模块',
            'button_title'      => '添加模块',
            'accordion_title'   => '添加模块',
            'fields'            => array(

                array(
                    'id'        => 'modular_title',
                    'type'      => 'text',
                    'title'     => '模块标题',
                    'default'   => '模块标题',
                    'dependency'=> array( 'modular_type', 'any', '1,2,3,4,5,6,7,8,9,10,11,13,14' ),
                ),
                array(
                    'id'        => 'modular_subtitle',
                    'type'      => 'text',
                    'title'     => '标题别名',
                    'desc'      => '一般情况下此处填写英文名',
                    'default'   => '自定义文本描述，通常为英文别名',
                    'dependency'=> array( 'modular_type', 'any', '1,2,4,6,7,10,13,14' ),
                ),

                array(
                    'id'        => 'modular_type',
                    'type'      => 'image_select',
                    'title'     => '选择模块',
                    //'inline'    => true,
                    //'class'     => 'horizontal',
                    'options'   => array(
                        '1'     => get_stylesheet_directory_uri() . '/static/images/admin/index-1.png',
                        '13'    => get_stylesheet_directory_uri() . '/static/images/admin/index-13.png',
                        '4'     => get_stylesheet_directory_uri() . '/static/images/admin/index-4.png',
                        '14'    => get_stylesheet_directory_uri() . '/static/images/admin/index-14.png',
                        '3'     => get_stylesheet_directory_uri() . '/static/images/admin/index-3.png',
                        '7'     => get_stylesheet_directory_uri() . '/static/images/admin/index-7.png',
                        '2'     => get_stylesheet_directory_uri() . '/static/images/admin/index-2.png',
                        '5'     => get_stylesheet_directory_uri() . '/static/images/admin/index-5.png',
                        '6'     => get_stylesheet_directory_uri() . '/static/images/admin/index-6.png',
                        '10'    => get_stylesheet_directory_uri() . '/static/images/admin/index-10.png',
                        '8'     => get_stylesheet_directory_uri() . '/static/images/admin/index-8.png',
                        '11'    => get_stylesheet_directory_uri() . '/static/images/admin/index-11.png',
                        '12'    => get_stylesheet_directory_uri() . '/static/images/admin/index-12.png',
                        '9'     => get_stylesheet_directory_uri() . '/static/images/admin/index-9.png',
                    ),
                    'default'   => '1',
                ),

                array(
                    'id'        => 'modular_no_mobile',
                    'type'      => 'checkbox',
                    'title'     => '',
                    'label'     => '禁止手机端显示此模块',
                    'help'      => '开启后手机端浏览将不显示这个模块',
                    'dependency'=> array( 'modular_type', 'any', '1,2,3,4,5,6,7,8,10,11,12,13,14' ),
                    'default'   => false
                ),

                // 调用文章
                array(
                    'id'        => 'modular_cat_or_post',
                    'type'      => 'select',
                    'title'     => '调用文章',
                    'subtitle'  => '选择「指定分类」请勾选下分类目录，选择「指定文章」请设置一下指定文章',
                    'options'   => array(
                        '1'     => '请选择调用内容',
                        'cat'   => '指定分类',
                        'post'  => '指定文章',
                    ),
                    'default'   => '1',
                    'dependency'=> array( 'modular_type', 'any', '1,2,4' ),
                ),
                array(
                    'id'        => 'modular_category',
                    'type'      => 'select',
                    'title'     => '选择分类',
                    //'inline'    => true,
                    'chosen'    => true,
                    'multiple'  => true,
                    'sortable'  => true,
                    'options'   => 'categories',
                    'subtitle'  => '如果分类下没有文章，此处将不显示此分类目录',
                    //'dependency'=> array( 'modular_cat_or_post', 'any', 'cat' ),
                    'dependency'=> array( 'modular_type', 'any', '1,2,4,13,14' ),
                ),
                array(
                    'id'        => 'modular_posts_id',
                    'type'      => 'select',
                    'title'     => '指定文章',
                    'chosen'    => true,
                    'multiple'  => true,
                    'sortable'  => true,
                    'ajax'      => true,
                    'options'   => 'posts',
                    'placeholder'=> '输入关键词进行搜索，不少于三个字',
                    //'dependency'=> array( 'modular_cat_or_post', 'any', 'post' ),
                    'dependency'=> array( 'modular_type', 'any', '1,2,4' ),
                ),
                // 调用文章 结束

                // 缩略图尺寸
                array(
                    'id'         => 'post_img_width',
                    'type'       => 'spinner',
                    'title'      => '图片宽度',
                    'subtitle'   => '自定义缩略图尺寸，默认宽高500*500，最小宽度264px，高度随意，如果缩略图模糊，可同比放大尺寸，比如：800*800，没有固定尺寸，建议自己设置尺寸进行调试，达到自己满意的比例',
                    'max'        => 10000,
                    'min'        => 264,
                    'step'       => 1,
                    'unit'       => 'px',
                    'attributes' => array('style'=> 'width: 100%;'),
                    'dependency' => array( 'modular_type', 'any', '2,4,14' ),
                ),
                array(
                    'id'         => 'post_img_height',
                    'type'       => 'spinner',
                    'title'      => '图片高度',
                    //'subtitle' => 'max:1 | min:0 | step:0.1 | unit:px',
                    'max'        => 10000,
                    'min'        => 0,
                    'step'       => 1,
                    'unit'       => 'px',
                    'attributes' => array('style'=> 'width: 100%;'),
                    'dependency' => array( 'modular_type', 'any', '2,4,14' ),
                ),
                // 缩略图尺寸 结束

                // 关于我们 模块设置
                array(
                    'id'        => 'modular_3_type',
                    'type'      => 'radio',
                    'title'     => '',
                    'inline'    => true,
                    'options'   => array(
                        '1'     => '图像居左',
                        '2'     => '图像居右',
                    ),
                    'default'   => '1',
                    'dependency'=> array( 'modular_type', 'any', '3' ),
                ),
                array(
                    'id'        => 'modular_3_img',
                    'type'      => 'media',
                    'title'     => '特色图像',
                    'help'      => '建议尺寸：545×405 px',
                    'settings'  => array(
                        'button_title' => '上传图像',
                        'frame_title'  => '选择图像',
                        'insert_title' => '插入图像',
                    ),
                    'dependency'=> array( 'modular_type', 'any', '3' ),
                ),
                array(
                    'id'            => 'modular_3_describe',
                    'type'          => 'wp_editor',
                    'title'         => '模块描述内容',
                    'subtitle'      => '可使用html...',
                    'height'        => '100px',
                    'media_buttons' => false,
                    'tinymce'       => false,
                    'dependency'    => array( 'modular_type', 'any', '3' ),
                ),
                // 关于我们 模块设置 结束

                // 部分模块可设置 跳转链接「查看更多」
                array(
                    'id'            => 'modular_url',
                    'type'          => 'text',
                    'title'         => '跳转链接',
                    'attributes'    => array('style'=> 'width: 100%;'),
                    'subtitle'      => '链接带上 http:// 或者 https://',
                    'dependency'    => array( 'modular_type', 'any', '1,2,3,4' ),
                ),

                // 自定义图文 模块设置
                array(
                    'id'    => 'modular_5',
                    'type'  => 'tabbed',
                    'title' => '',
                    'tabs'  => array(

                        array(
                            'title'  => '栏目-1 设置',
                            'fields' => array(
                                array(
                                    'id'        => 'modular_5_img',
                                    'type'      => 'media',
                                    'title'     => '栏目-1 特色图像',
                                    'help'      => '建议尺寸：560×315 px',
                                    'settings'  => array(
                                        'button_title' => '上传图像',
                                        'frame_title'  => '选择图像',
                                        'insert_title' => '插入图像',
                                    ),
                                ),
                                array(
                                    'id'        => 'modular_title_5',
                                    'type'      => 'text',
                                    'title'     => '栏目-1 标题',
                                ),
                                array(
                                    'id'        => 'modular_describe_5',
                                    'type'      => 'textarea',
                                    'title'     => '栏目-1 描述',
                                    'placeholder'=> '输入一段简短的文字说明',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                                array(
                                    'id'        => 'modular_5_url',
                                    'type'      => 'text',
                                    'title'     => '栏目1 - 跳转链接',
                                    'help'      => '输入要跳转到的页面链接',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),

                        array(
                            'title'  => '栏目-2 设置',
                            'fields' => array(
                                array(
                                    'id'        => 'modular_5_img_2',
                                    'type'      => 'media',
                                    'title'     => '栏目-2 特色图像',
                                    'help'      => '建议尺寸：560×315 px',
                                    'settings'  => array(
                                        'button_title' => '上传图像',
                                        'frame_title'  => '选择图像',
                                        'insert_title' => '插入图像',
                                    ),
                                ),
                                array(
                                    'id'        => 'modular_title_5_2',
                                    'type'      => 'text',
                                    'title'     => '栏目-2 标题',
                                ),
                                array(
                                    'id'        => 'modular_describe_5_2',
                                    'type'      => 'textarea',
                                    'title'     => '栏目-2 描述',
                                    'placeholder'=> '输入一段简短的文字说明',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                                array(
                                    'id'        => 'modular_5_url_2',
                                    'type'      => 'text',
                                    'title'     => '栏目2 - 跳转链接',
                                    'help'      => '输入要跳转到的页面链接',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),

                    ),
                    'dependency'    => array( 'modular_type', 'any', '5' ),
                ),
                // 自定义图文 模块设置 结束

                // 合作伙伴 模块设置
                array(
                    'id'            => 'modular_6_logo',
                    'type'          => 'gallery',
                    'title'         => '上传合作伙伴 Logo',
                    'subtitle'      => '建议上传10个图片，尺寸 258×92 px',
                    'add_title'     => '继续添加',
                    'edit_title'    => '编辑',
                    'clear_title'   => '全部删除',
                    'dependency'    => array( 'modular_type', 'any', '6' ),
                ),
                // 合作伙伴 模块设置 结束


                // 特点介绍 模块设置
                array(
                    'id'                => 'add_feature',
                    'type'              => 'group',
                    'title'             => '',
                    'button_title'      => '添加特点介绍',
                    'accordion_title'   => '添加特点介绍',
                    'fields'            => array(
                        array(
                            'id'        => 'feature_title',
                            'type'      => 'text',
                            'title'     => '标题',
                        ),
                        array(
                            'id'        => 'feature_describe',
                            'type'      => 'textarea',
                            'title'     => '描述内容',
                        ),
                        array(
                            'id'        => 'feature_icon',
                            'type'      => 'icon',
                            'title'     => '选择图标',
                        ),
                    ),
                    'dependency'        => array( 'modular_type', 'any', '7,10' ),
                ),

                array(
                    'id'        => 'feature_color',
                    'type'      => 'palette',
                    'title'     => '选择配色',
                    'options'   => array(
                        'aa'    => array( '#f10', '#f10'),
                        'a'     => array( '#f44336', '#f44336'),
                        'b'     => array( '#e91e63', '#e91e63'),
                        'c'     => array( '#9c27b0', '#9c27b0'),
                        'd'     => array( '#673ab7', '#673ab7'),
                        'e'     => array( '#3f51b5', '#3f51b5'),
                        'f'     => array( '#2196f3', '#2196f3'),
                        'g'     => array( '#03a9f4', '#03a9f4'),
                        'h'     => array( '#00bcd4', '#00bcd4'),
                        'i'     => array( '#009688', '#009688'),
                        'j'     => array( '#4caf50', '#4caf50'),
                        'k'     => array( '#ff9800', '#ff9800'),
                        'l'     => array( '#ff5722', '#ff5722'),
                    ),
                    'dependency'=> array( 'modular_type', 'any', '7,10' ),
                ),
                // 特点介绍 模块设置 结束

                // 数字模块 设置
                array(
                    'id'    => 'modular_8',
                    'type'  => 'tabbed',
                    'title' => '',
                    'tabs'  => array(

                        array(
                            'title'  => '第1组',
                            'fields' => array(

                                array(
                                    'id'        => 'modular_8_count_1',
                                    'type'      => 'text',
                                    'title'     => '第1组-数字',
                                ),
                                array(
                                    'id'        => 'modular_8_title_1',
                                    'type'      => 'text',
                                    'title'     => '第1组-标题',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),
                        array(
                            'title'  => '第2组',
                            'fields' => array(

                                array(
                                    'id'        => 'modular_8_count_2',
                                    'type'      => 'text',
                                    'title'     => '第2组-数字',
                                ),
                                array(
                                    'id'        => 'modular_8_title_2',
                                    'type'      => 'text',
                                    'title'     => '第2组-标题',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),
                        array(
                            'title'  => '第3组',
                            'fields' => array(

                                array(
                                    'id'        => 'modular_8_count_3',
                                    'type'      => 'text',
                                    'title'     => '第3组-数字',
                                ),
                                array(
                                    'id'        => 'modular_8_title_3',
                                    'type'      => 'text',
                                    'title'     => '第3组-标题',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),
                        array(
                            'title'  => '第4组',
                            'fields' => array(

                                array(
                                    'id'        => 'modular_8_count_4',
                                    'type'      => 'text',
                                    'title'     => '第4组-数字',
                                ),
                                array(
                                    'id'        => 'modular_8_title_4',
                                    'type'      => 'text',
                                    'title'     => '第4组-标题',
                                    'attributes'=> array('style'=> 'width: 100%;'),
                                ),
                            ),
                        ),

                    ),
                    'dependency' => array( 'modular_type', 'any', '8' ),
                ),
                array(
                    'id'         => 'modular_8_bg',
                    'type'       => 'accordion',
                    'title'      => '',
                    'accordions' => array(

                        array(
                          'title'  => '设置模块 背景颜色 或 图片',
                          'fields' => array(
                            array(
                                'id'        => 'modular_8_bg_img',
                                'type'      => 'media',
                                'title'     => '背景图片',
                                'settings'  => array(
                                    'button_title' => '上传图像',
                                    'frame_title'  => '选择图像',
                                    'insert_title' => '插入图像',
                                ),
                            ),
                            array(
                                'id'    => 'modular_8_bg_color',
                                'type'  => 'color',
                                'title' => '背景颜色',
                            ),
                          )
                        ),

                    ),
                    'dependency'  => array( 'modular_type', 'any', '8' ),
                ),
                // 数字模块 设置结束

                // 在线留言 模块设置
		        array(
		            'type'        => 'subheading',
		            'content'     => '在线留言的相关设置，请到「后台-网站优化-在线留言」中进行设置，此处只是方便把表单添加到首页并调整模块排序',
		            'dependency'  => array( 'modular_type', 'any', '11' )
		        ),
                // 在线留言 模块设置 结束

				// 视频模块
                array(
                    'id'            => 'video_sub_title',
                    'type'          => 'text',
                    'title'         => '小标题',
                    'dependency'    => array( 'modular_type', 'any', '12' )
                ),
                array(
                    'id'            => 'video_title',
                    'type'          => 'text',
                    'title'         => '大标题',
                    'dependency'    => array( 'modular_type', 'any', '12' )
                ),
                array(
                    'id'            => 'video_url',
                    'type'          => 'textarea',
                    'title'         => '视频链接',
                    'dependency'    => array( 'modular_type', 'any', '12' )
                ),
                array(
                    'id'            => 'video_bg_img',
                    'type'          => 'media',
                    'title'         => '背景图片',
                    'subtitle'      => '建议尺寸：1440×375 px',
                    'settings'      => array(
                        'button_title' => '上传图片',
                        'frame_title'  => '选择图片',
                        'insert_title' => '插入图片',
                    ),
                    'dependency'    => array( 'modular_type', 'any', '12' )
                ),
				// 视频模块 结束

                //模块-9
                array(
                    'id'        => 'modular_9_content',
                    'type'      => 'code_editor',
                    'title'     => '自定义代码',
                    'help'     => '支持html、css、js...',
                    'settings' => array(
                        'theme'  => 'shadowfox',
                        'mode'   => 'htmlmixed',
                    ),
                    'dependency'=> array( 'modular_type', 'any', '9' ),
                ),

            ),

        ),
    ),
) );

// ----------------------------------------
// 文章页面
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '文章页面',
    'priority' => 4,
    'fields'   => array(

        array(
            'id'      => 'post_header_banner',
            'type'    => 'checkbox',
            'title'   => '',
            'subtitle'=> 'Baanner图片来自于分类目录中设置的Banner图片',
            'label'   => '文章顶部不显示Banner图片',
            'default' => false
        ),
        array(
            'id'      => 'post_no_sidebar_all',
            'type'    => 'checkbox',
            'title'   => '',
            'help'    => '开启后，所有文章页面都显示为单栏样式。',
            'label'   => '所有文章均使用单栏文章页',
            'default' => false
        ),
        array(
            'id'      => 'xintheme_post_indent',
            'type'    => 'checkbox',
            'title'   => '',
            'help'    => '用CSS定义每段首行缩进2个字符。',
            'label'   => '段落首行缩进',
            'default' => false
        ),
        array(
            'id'      => 'xintheme_single_time',
            'type'    => 'checkbox',
            'title'   => '',
            'label'   => '文章发布时间',
            'default' => true
        ),
        array(
            'id'      => 'xintheme_single_views',
            'type'    => 'checkbox',
            'title'   => '',
            'label'   => '文章浏览量',
            'default' => true
        ),
        array(
            'id'      => 'xintheme_single_comments',
            'type'    => 'checkbox',
            'title'   => '',
            'label'   => '文章评论',
            'default' => true
        ),

        array(
            'id'      => 'cat_type_single',
            'type'    => 'checkbox',
            'title'   => '',
            'label'   => '自动使用产品文章样式',
            'help'   => '在【文章 - 分类目录 - 布局样式】中选择【产品列表】样式后将自动把分类下所有文章使用产品文章样式',
            'default' => false
        ),

        /*
        array(
            'id'      => 'produc_single_button',
            'type'    => 'switcher',
            'title'   => '<br><br>产品文章页 自定义默认按钮',
            'after'   => '<p class="cs-text-muted">开启后，所有产品文章页摘要下面将显示这里添加的自定义按钮，如果编辑文章的时候有单独设置自定义按钮，则优先显示文章内添加的按钮。</p>',
            'default' => false
        ),
        */

        //添加按钮
        array(
            'id'                => 'add_button',
            'type'              => 'group',
            'title'             => '添加按钮',
            'subtitle'          => '产品文章页默认按钮，所有产品文章页摘要下面将显示这里添加的自定义按钮，如果编辑文章的时候有单独设置自定义按钮，则优先显示文章内添加的按钮。',
            'button_title'      => '添加按钮',
            'accordion_title'   => '添加按钮',
            'fields'            => array(

                array(
                    'id'            => 'produc_button_type',
                    'type'          => 'radio',
                    'title'         => '菜单类型',
                    'class'         => 'horizontal',
                    'options'       => array(
                        'link'      => '跳转链接',
                        'img'       => '弹出图像',
                        'qq'        => 'QQ在线咨询',
                    ),
                    'default'       => 'link',
                ),
                array(
                    'id'            => 'button_title',
                    'type'          => 'text',
                    'title'         => '按钮文本',
                ),
                array(
                    'id'            => 'button_icon',
                    'type'          => 'icon',
                    'title'         => '按钮图标',
                ),
                array(
                    'id'            => 'button_color',
                    'type'          => 'color',
                    'title'         => '按钮颜色',
                    //'default'       => '#666',
                ),
                array(
                    'id'            => 'button_url',
                    'type'          => 'text',
                    'title'         => '跳转链接',
                    'attributes'    => array('style'=> 'width: 100%;'),
                    'desc'          => '记得输入： http:// 或者 https://',
                    'dependency'    => array( 'produc_button_type', 'any', 'link' )
                ),
                array(
                    'id'            => 'button_qq',
                    'type'          => 'text',
                    'title'         => 'QQ号码',
                    'dependency'    => array( 'produc_button_type', 'any', 'qq' )
                ),
                array(
                    'id'            => 'button_img',
                    'type'          => 'media',
                    'title'         => '上传图像',
                    'after'         => '<p class="cs-text-muted">建议尺寸 200*200</p>',
                    'settings'      => array(
                        'button_title' => '上传图像',
                        'frame_title'  => '选择图像',
                        'insert_title' => '插入图像',
                    ),
                    'dependency'    => array( 'produc_button_type', 'any', 'img' )
                ),


            ),
            //'dependency'    => array( 'produc_single_button', '==', true )
        ), 


        //广告设置
        array(
            'id'    => 'single_ad',
            'type'  => 'tabbed',
            'title' => '广告设置',
            'tabs'  => array(
                array(
                    'title'  => '内容顶部广告',
                    'fields' => array(
                        array(
                            'id'        => 'single_ad_top',
                            'type'      => 'media',
                            'title'     => '广告图片',
                            'help'      => '建议图片宽度为785 px',
                            'settings'  => array(
                                'button_title' => '上传图片',
                                'frame_title'  => '选择图片',
                                'insert_title' => '插入图片',
                            ),
                        ),
                        array(
                            'id'        => 'single_ad_top_url',
                            'type'      => 'text',
                            'title'     => '跳转链接',
                            'attributes'=> array('style'=> 'width: 100%;'),
                        ),
                    ),
                ),
                array(
                    'title'  => '内容底部广告',
                    'fields' => array(
                        array(
                            'id'        => 'single_ad_bottom',
                            'type'      => 'media',
                            'title'     => '广告图片',
                            'help'      => '建议图片宽度为785 px',
                            'settings'  => array(
                                'button_title' => '上传图片',
                                'frame_title'  => '选择图片',
                                'insert_title' => '插入图片',
                            ),
                        ),
                        array(
                            'id'        => 'single_ad_bottom_url',
                            'type'      => 'text',
                            'title'     => '跳转链接',
                            'attributes'=> array('style'=> 'width: 100%;'),
                        ),
                    ),
                ),

            ),

        ),
        //广告设置结束


    )
) );

// ----------------------------------------
// 页脚样式
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '页脚设置',
    'priority' => 5,
    'fields'   => array(

        array(
            'id'        => 'foot_type',
            'type'      => 'image_select',
            'title'     => '选择页脚样式',
            //'inline'    => true,
            //'class'     => 'horizontal',
            'options'   => array(
                '1'     => get_stylesheet_directory_uri() . '/static/images/admin/footer-1.png',
                '2'     => get_stylesheet_directory_uri() . '/static/images/admin/footer-2.png',
                '3'     => get_stylesheet_directory_uri() . '/static/images/admin/footer-3.png',
            ),
            'default'   => '1',
        ),
        array(
            'type'      => 'subheading',
            'content'   => '页脚菜单请到菜单中设置',
            'dependency'=> array( 'foot_type', 'any', '1,2' )
        ),
        //array(
            //'id'          => 'foot_menus',
            //'type'        => 'select',
            //'title'       => '页脚菜单',
            //'placeholder' => '选择菜单',
            //'options'     => 'menus',
            //'dependency'  => array( 'foot_type', 'any', '2' )
        //),
        array(
            'id'        => 'footer2_logo',
            'type'      => 'media',
            'title'     => '页脚Logo',
            'subtitle'  => '建议205×58 px',
            'add_title' => '上传Logo',
            'dependency'=> array( 'foot_type', 'any', '2' )
        ),
        array(
            'id'        => 'footer2_describe',
            'type'      => 'textarea',
            'title'     => '页脚描述',
            'subtitle'  => '支持html标签，换行请使用br标签',
            'dependency'=> array( 'foot_type', 'any', '2' )
        ),

        array(
            'id'        => 'footer_qr_code_title',
            'type'      => 'text',
            'title'     => '二维码描标题',
            'default'   => '扫码关注公众号',
            'dependency'=> array( 'foot_type', 'any', '2' )
        ),
        array(
            'id'        => 'footer_qr_code',
            'type'      => 'media',
            'title'     => '网站底部二维码',
            'subtitle'  => '建议200×200 px',
            'add_title' => '上传二维码',
            'dependency'=> array( 'foot_type', 'any', '1,2' )
        ),
        array(
            'id'        => 'footer_qr_code_text',
            'type'      => 'text',
            'title'     => '二维码描述文本',
            'default'   => '扫码关注公众号',
            'dependency'=> array( 'foot_type', 'any', '1' )
        ),
        array(
            'id'              => 'footer2_contact',
            'type'            => 'group',
            'title'           => '',
            'button_title'    => '添加联系信息',
            'accordion_title' => '添加联系信息',
            'fields'          => array(
                array(
                    'id'        => 'footer2_contact_describe',
                    'type'      => 'text',
                    'title'     => '填写联系方式',
                ),
                array(
                    'id'            => 'footer2_contact_icon',
                    'type'          => 'icon',
                    'title'         => '选择图标',
                ),
            ),
            'dependency'=> array( 'foot_type', 'any', '2' )
        ),
        array(
            'id'        => 'footer_copyright',
            'type'      => 'textarea',
            'title'     => '自定义页脚版权信息',
            'after'     => '<p class="cs-text-muted">如需添加链接，请使用 a 标签...</p>',
        ),
        array(
            'id'        => 'footer_icp',
            'type'      => 'text',
            'title'     => 'ICP备案号',
        ),
        array(
            'id'        => 'footer_gaba',
            'type'      => 'text',
            'title'     => '公安备案号',
        ),
        array(
            'id'        => 'footer_gaba_url',
            'type'      => 'text',
            'title'     => '公安备案跳转链接',
        ),
        array(
            'id'        => 'foot_link',
            'type'      => 'checkbox',
            'title'     => '',
            'label'     => '友情链接',
            'help'      => '友情链接在【后台 - 链接】 中添加',
            'default'   => true
        ),
        array(
            'id'        => 'xintheme_link',
            'type'      => 'checkbox',
            'title'     => '',
            'label'     => '页脚主题版权信息',
            'default'   => true
        ),
        array(
            'id'        => 'timer_stop',
            'type'      => 'checkbox',
            'title'     => '',
            'label'     => '显示网站加载时间',
        ),

  )
) );

// ----------------------------------------
// 手机端设置
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '手机端设置',
    'priority' => 6,
    'fields'   => array(

        array(
            'id'      => 'mobile_foot_menu_sw',
            'type'    => 'switcher',
            'title'   => '手机端底部菜单',
            'default' => false
        ),
        array(
            'id'              => 'add_mobile_foot_menu',
            'type'            => 'group',
            'title'           => '手机端底部菜单设置',
            'button_title'    => '添加手机端底部菜单',
            'accordion_title' => '添加手机端底部菜单',
            'fields'          => array(
                array(
                    'id'            => 'mobile_foot_menu_text',
                    'type'          => 'text',
                    'title'         => '菜单名称',
                    //'dependency'  => array( 'mobile_foot_menu_type_user', '==', false )
                ),
                array(
                    'id'            => 'mobile_foot_menu_type',
                    'type'          => 'radio',
                    'title'         => '菜单类型',
                    'class'         => 'horizontal',
                    'options'       => array(
                        'link'      => '跳转链接',
                        'img'       => '弹出图像',
                        //'user'        => '登陆/用户中心',
                    ),
                    'default'       => 'link',
                ),
                array(
                    'id'            => 'mobile_foot_menu_icon',
                    'type'          => 'icon',
                    'title'         => '选择菜单图标',
                    //'dependency'  => array( 'mobile_foot_menu_type_user', '==', false )
                ),
                array(
                    'id'            => 'mobile_foot_menu_img',
                    'type'          => 'media',
                    'title'         => '上传图像',
                    'after'         => '<p class="cs-text-muted">建议尺寸 200*200</p>',
                    'settings'      => array(
                        'button_title' => '上传图像',
                        'frame_title'  => '选择图像',
                        'insert_title' => '插入图像',
                    ),
                    'dependency'    => array( 'mobile_foot_menu_type', '==', 'img' )
                ),
                array(
                    'id'            => 'mobile_foot_menu_img_text',
                    'type'          => 'text',
                    'title'         => '图像标题',
                    'dependency'    => array( 'mobile_foot_menu_type', '==', 'img' )
                ),
                array(
                    'id'            => 'mobile_foot_menu_url',
                    'type'          => 'text',
                    'title'         => '跳转链接',
                    'after'         => '<p class="cs-text-muted">不要忘记了“http(s)://”</p>',
                    'attributes'    => array('style'=> 'width: 100%;'),
                    'dependency'    => array( 'mobile_foot_menu_type', '==', 'link' )
                ),

          ),
          'dependency'  => array( 'mobile_foot_menu_sw', '==', true )
        ),


  )
) );

// ----------------------------------------
// 客服工具
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '客服工具',
    'priority' => 7,
    'fields'   => array(

        array(
            'id'            => 'consultation_weixin_img',
            'type'          => 'media',
            'title'         => '微信二维码',
            'desc'          => '上传您的微信二维码(165*165)。',
            'add_title'     => '上传二维码',
        ),
        array(
            'id'            => 'consultation_weixin_txt',
            'type'          => 'text',
            'title'         => '微信二维码 - 描述',
            'after'         => '<p class="cs-text-muted">如：关注微信公众号</p>',
        ),
        array(
            'id'            => 'consultation_qq',
            'type'          => 'text',
            'title'         => '客服QQ号码',
            'after'         => '<p class="cs-text-muted">输入QQ号码，当用户点击QQ图标的时候会打开与您的QQ临时会话窗口。</p>',
        ),
        array(
            'id'            => 'consultation_weibo_url',
            'type'          => 'text',
            'title'         => '官方新浪微博',
            'after'         => '<p class="cs-text-muted">输入您的微博链接地址。</p>',
        ),
        array(
            'id'            => 'consultation_email_url',
            'type'          => 'text',
            'title'         => '邮箱咨询链接',
            'after'         => '<p class="cs-text-muted">输入您的电子邮箱咨询链接。</p>',
        ),
        array(
            'id'            => 'consultation_tel',
            'type'          => 'text',
            'title'         => '联系电话',
            'after'         => '<p class="cs-text-muted">输入您的联系电话。</p>',
        ),

  )
) );

// ----------------------------------------
// 网站配色
// ----------------------------------------
CSF::createSection( $prefix, array(
    'title'    => '网站配色',
    'priority' => 999,
    'fields'   => array(

        array(
            'id'        => 'zing_color',
            'type'      => 'palette',
            'title'     => '网站主色调',
            'options'   => array(
                'aa'     => array( '#f10', '#f10'),
                'a'     => array( '#f44336', '#f44336'),
                'b'     => array( '#e91e63', '#e91e63'),
                'c'     => array( '#9c27b0', '#9c27b0'),
                'd'     => array( '#673ab7', '#673ab7'),
                'e'     => array( '#3f51b5', '#3f51b5'),
                'f'     => array( '#2196f3', '#2196f3'),
                'g'     => array( '#03a9f4', '#03a9f4'),
                'h'     => array( '#00bcd4', '#00bcd4'),
                'i'     => array( '#009688', '#009688'),
                'j'    => array( '#4caf50', '#4caf50'),
                'k'    => array( '#ff9800', '#ff9800'),
                'l'    => array( '#ff5722', '#ff5722'),
            ),
        ),
        array(
            'id'        => 'footer_color',
            'type'      => 'palette',
            'title'     => '页脚配色',
            'options'   => array(
                'aa'     => array( '#f10', '#f10'),
                'm'    => array( '#152545', '#152545'),
                'a'     => array( '#f44336', '#f44336'),
                'b'     => array( '#e91e63', '#e91e63'),
                'c'     => array( '#9c27b0', '#9c27b0'),
                'd'     => array( '#673ab7', '#673ab7'),
                'e'     => array( '#3f51b5', '#3f51b5'),
                'f'     => array( '#2196f3', '#2196f3'),
                'g'     => array( '#03a9f4', '#03a9f4'),
                'h'     => array( '#00bcd4', '#00bcd4'),
                'i'     => array( '#009688', '#009688'),
                'j'    => array( '#4caf50', '#4caf50'),
                'k'    => array( '#ff9800', '#ff9800'),
                'l'    => array( '#ff5722', '#ff5722'),
            ),
        ),

  )
) );

// 添加修改按钮 
function add_blue_pencil( $wp_customize ) {

    $wp_customize->selective_refresh->add_partial(
        'xintheme_customize[logo]',
        array(
            'selector'        => '.logo',
        )
    );
    $wp_customize->selective_refresh->add_partial(
        'xintheme_customize[banner]',
        array(
            'selector'        => '#responsive-309391',
        )
    );
    $wp_customize->selective_refresh->add_partial(
        'xintheme_customize[index_modular]',
        array(
            'selector'        => '.module-full-screen',
        )
    );


}
add_action( 'customize_register', 'add_blue_pencil' );

if( is_user_logged_in() ){
	add_action('wp_head',function(){ ?>
	
	<style type="text/css">
	    .customize-partial-edit-shortcut-xintheme_customize-banner,.customize-partial-edit-shortcut-xintheme_customize-index_modular{margin-left: 50px;margin-top: 20px}
	</style>
	
	<?php });
}

// 在自定义设置页面添加css
add_action('admin_enqueue_scripts',function(){ ?>

<style type="text/css">
	.quicktags-toolbar{display:none}
    .quicktags-toolbar+textarea{min-height:200px}
    .button.button-primary.csf-cloneable-add{width:100%;text-align:center;padding:5px 0;font-size:15px}

    /*清除颜色样式*/
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--active:before{z-index:9999}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette span{width:20px;height:40px;vertical-align:inherit}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette:first-child span{width:40px;height:40px}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette:first-child span{display:none}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette:first-child span:first-child{display:block}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette:first-child span::before{content:"";position:absolute;left:0;top:0;right:0;bottom:0;-webkit-clip-path:polygon(0 0,0 100px,100px 100px,0 0);background:#fff}
    .csf-customize-field[data-unique-id="xintheme_customize"] .csf-field-palette .csf--palette:first-child span::after{content:"";position:absolute;left:0;top:0;right:0;bottom:0;-webkit-clip-path:polygon(100px 99px,100px 0,1px -1px,100px 99px);background:#fff}
    /*清除颜色样式结束*/

    /*选中样式*/
    #customize-control-xintheme_customize-header_type .csf-field-image_select .csf--active,
    #customize-control-xintheme_customize-foot_type .csf-field-image_select .csf--active{border: 4px solid #F44336}
    #customize-control-xintheme_customize-header_type .csf-field-image_select .csf--image:before,
    #customize-control-xintheme_customize-foot_type .csf-field-image_select .csf--image:before{background-color:#F44336}
    #customize-control-xintheme_customize-index_modular .csf-field-image_select .csf--image{max-width: 45.5%;border: 3px solid #eee;}
    #customize-control-xintheme_customize-index_modular .csf-field-image_select .csf--image:nth-child(2){margin-right:0px}
    #customize-control-xintheme_customize-index_modular .csf-field-image_select .csf--active{border: 3px solid #F44336}
    #customize-control-xintheme_customize-index_modular .csf-field-image_select .csf--image:before{background-color:#F44336}

</style>

<?php });

