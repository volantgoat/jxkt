<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$prefix_post_opts = 'extend_info';

//
// Create a metabox
//
CSF::createMetabox( $prefix_post_opts, array(
	'title'        => '文章扩展选项',
	'post_type'    => 'post',
	//'show_restore' => true,
) );

//
// Create a section
//
CSF::createSection( $prefix_post_opts, array(
	'title'  => ' 布局设置',
	'icon'   => 'iconfont icon-buju',
	'fields' => array(

		array(
			'id'		=> 'post_layout',
			'type'		=> 'image_select',
			'title'		=> '文章页样式',
			'options'	=> array(
				'news'	=> get_stylesheet_directory_uri() . '/static/images/admin/single-1.png',
				'grid'	=> get_stylesheet_directory_uri() . '/static/images/admin/single-2.png',
			),
			'default'   => 'news',
			'radio'     => true
		),
		array(
			'id'        => 'produc_img',
			'type'      => 'gallery',
			'title'     => '产品图集',
			'desc'      => '建议尺寸：500*500，或同比放大/缩小',
			'add_title' => '选择产品图',
			'edit_title'=> '编辑图集',
			'dependency'=> array('post_layout', 'any', 'grid' )
		),
		array(
			'id'		=> 'produc_abstract',
			'type'		=> 'textarea',
			'title'		=> '产品摘要',
			'after'		=> '<p class="cs-text-muted">自定义产品摘要，如留空，则自动调用文章首段文字...</p>',
			'dependency'=> array('post_layout', 'any', 'grid' )
		),
		//添加按钮
		array(
			'id'              => 'add_button',
			'type'            => 'group',
			'title'           => '添加按钮',
			'button_title'    => '添加按钮',
			'accordion_title' => '添加按钮',
			'fields'          => array(

				array(
					'id'			=> 'produc_button_type',
					'type'			=> 'radio',
					'title'			=> '菜单类型',
					'class'			=> 'horizontal',
					'options'		=> array(
						'link'		=> '跳转链接',
						'img'		=> '弹出图像',
						'qq'		=> 'QQ在线咨询',
					),
					'default'		=> 'link',
				),
				array(
					'id'			=> 'button_title',
					'type'			=> 'text',
					'title'			=> '按钮文本',
				),
				array(
				    'id'			=> 'button_icon',
				    'type'			=> 'icon',
				    'title'			=> '按钮图标',
				),
				array(
					'id'      		=> 'button_color',
					'type'    		=> 'color',
					'title'   		=> '按钮颜色',
					//'default' 		=> '#666',
				),
				array(
				    'id'			=> 'button_url',
				    'type'			=> 'text',
				    'title'			=> '跳转链接',
				    'attributes'	=> array('style'=> 'width: 100%;'),
				    'desc'			=> '记得输入： http:// 或者 https://',
				    'dependency'    => array( 'produc_button_type', 'any', 'link' )
				),
				array(
					'id'			=> 'button_qq',
					'type'			=> 'text',
					'title'			=> 'QQ号码',
					'dependency'    => array( 'produc_button_type', 'any', 'qq' )
				),
				array(
					'id'			=> 'button_img',
					'type'        	=> 'media',
					'title'      	=> '上传图像',
					'after'			=> '<p class="cs-text-muted">建议尺寸 200*200</p>',
					'settings'      => array(
						'button_title' => '上传图像',
						'frame_title'  => '选择图像',
						'insert_title' => '插入图像',
					),
					'dependency'    => array( 'produc_button_type', 'any', 'img' )
				),


			),
			'dependency' => array('post_layout', 'any', 'grid' )

		),


		array(
			'id'		=> 'no_sidebar',
			'type'		=> 'switcher',
			'title'   	=> '',
			//'dependency'=> array( 'post_layout', 'any', 'news' ),
			'label'		=> '使用单栏样式',
			'default' 	=> false
		),



  )
) );

//
// Create a section
//
CSF::createSection( $prefix_post_opts, array(
	'title'  => ' SEO设置',
	'icon'   => 'iconfont icon-wz-seo',
	'fields' => array(

        array(
            'id'    => 'seo_title',
            'type'  => 'text',
            'title' => 'SEO-标题',
            'after' => '<div class="cs-text-muted">留空则调用文章标题</div>'
        ),
        array(
            'id'    => 'seo_keywords',
            'type'  => 'text',
            'title' => 'SEO-关键词',
            'after' => '<div class="cs-text-muted">多个关键词之间用英文逗号隔开</div>'
        ),
        array(
            'id'    => 'seo_description',
            'type'  => 'textarea',
            'title' => 'SEO-描述',
            'after' => '<div class="cs-text-muted">留空则调用文章摘要</div>'
        ),
			


  )
) );


$prefix_page_opts = 'page_seo';


CSF::createMetabox( $prefix_page_opts, array(
	'title'        => '<i class="iconfont icon-wz-seo"></i> SEO设置',
	'post_type'    => 'page',
	//'show_restore' => true,
) );

CSF::createSection( $prefix_page_opts, array(
	//'title'  => ' SEO设置',
	//'icon'   => 'iconfont icon-wz-seo',
	'fields' => array(

        array(
            'id'    => 'seo_title',
            'type'  => 'text',
            'title' => 'SEO-标题',
            'after' => '<div class="cs-text-muted">留空则调用文章标题</div>'
        ),
        array(
            'id'    => 'seo_keywords',
            'type'  => 'text',
            'title' => 'SEO-关键词',
            'after' => '<div class="cs-text-muted">多个关键词之间用英文逗号隔开</div>'
        ),
        array(
            'id'    => 'seo_description',
            'type'  => 'textarea',
            'title' => 'SEO-描述',
            'after' => '<div class="cs-text-muted">留空则调用文章摘要</div>'
        ),
			


  )
) );














