<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = '_prefix_taxonomy_options';

//
// Create taxonomy options
//
CSF::createTaxonomyOptions( $prefix, array(
  'taxonomy' => 'category',
) );

//
// Create a section
//
CSF::createSection( $prefix, array(
  'fields' => array(

        array(
            'id'     => 'cat_layout',
            'type'   => 'radio',
            'title'  => '布局样式',
            'class'  => 'horizontal',
            'options'=> array(
                'news'              => '新闻列表',
                'news-img'          => '新闻列表+缩略图',
                'grid'              => '产品列表+侧栏',
                'grid-no-sidebar'   => '产品列表 无侧栏',
            ),
            'default'   => 'news',
        ),

        // 缩略图尺寸
        array(
            'id'         => 'post_img_width',
            'type'       => 'spinner',
            'title'      => '图片宽度',
            'desc'       => '自定义缩略图尺寸，默认宽高500*500，最小宽度264px，高度随意，如果缩略图模糊，可同比放大尺寸，比如：800*800，没有固定尺寸，建议自己设置尺寸进行调试，达到自己满意的比例',
            'max'        => 10000,
            'min'        => 264,
            'step'       => 1,
            'unit'       => 'px',
            'attributes' => array('style'=> 'width: 100%;'),
            'dependency' => array( 'cat_layout', 'any', 'grid,grid-no-sidebar' ),
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
            'dependency' => array( 'cat_layout', 'any', 'grid,grid-no-sidebar' ),
        ),
        // 缩略图尺寸 结束

        array(
          'id'    => 'banner_cat_desc',
          'type'  => 'checkbox',
          'title' => '显示分类名称+描述',
          'label' => 'Banner图片上面显示分类目录名字以及分类描述...',
        ),
        array(
            'id'          => 'cat_banner_img',
            'type'        => 'media',
            'title'       => 'Banner图片',
            'desc'        => '建议尺寸：1920*300',
            'settings'    => array(
                'button_title' => '上传图像',
                'frame_title'  => '选择图像',
                'insert_title' => '插入图像',
            ),
        ),

        array(
          'type'    => 'notice',
          'style'   => 'success',
          'content' => 'SEO相关信息设置，非必填项，可留空',
          'attributes'   => array('style'=> 'width: 95%;'),
        ),

        array(
            'id'    => 'seo_title',
            'type'  => 'text',
            'title' => 'SEO-分类标题',
            'desc'  => '留空则默认显示分类标题+网站副标题',
            'attributes'   => array('style'=> 'width: 95%;'),
        ),

        array(
            'id'    => 'seo_description',
            'type'  => 'textarea',
            'title' => 'SEO-分类描述',
            'desc'  => '一段简单的分类描述文字',
            'attributes'   => array('style'=> 'width: 95%;'),
        ),

        array(
            'id'    => 'seo_keywords',
            'type'  => 'textarea',
            'title' => 'SEO-关键词',
            'desc'  => '多个关键词之间用英文逗号隔开',
            'attributes'   => array('style'=> 'width: 95%;'),
        ),


  )
) );

add_action('admin_head',function(){ ?>

<style type="text/css">

.csf-field.csf-field-notice{width:95%}

</style>

<?php });
