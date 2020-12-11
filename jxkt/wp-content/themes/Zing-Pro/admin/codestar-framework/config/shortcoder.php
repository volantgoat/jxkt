<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'csf_joy_shortcodes';

//
// Create a shortcoder
//
CSF::createShortcoder( $prefix, array(
  'button_title'   => '添加短代码',
  'select_title'   => '选择短代码',
  'insert_title'   => '插入短代码',
  'show_in_editor' => true,
  'gutenberg'      => array(
    'title'        => '【主题自带】短代码',
    'description'  => '【主题自带】短代码',
    'icon'         => 'editor-code',
    'category'     => 'widgets',
    'keywords'     => array( 'shortcode', 'csf', 'insert' ),
    'placeholder'  => '主题自带短代码...',
  )
) );

//
// 百度地图
//
CSF::createSection( $prefix, array(
	'title'     => '[baidu_map] 百度地图',
	//'view'      => 'group',
	'shortcode' => 'baidu_map',
	'fields'    => array(

		array(
          'id'		=> 'dtmishi',
          'type'	=> 'text',
          'title'	=> '应用AK',
          'attributes' => array(
            'style'    => 'width: 100%;'
          ),
          'after'	=> '<p class="cs-text-muted">百度地图没有密钥是不能调用的，获取地址：http://lbsyun.baidu.com/apiconsole/key</p>',
        ),
        array(
          'id'     => 'zuobiao',
          'type'   => 'text',
          'title' => '地址坐标',
          'attributes' => array(
            'style'    => 'width: 100%;'
          ),
          'after'  => '<p class="cs-text-muted">输入地址坐标，坐标获取地址：http://api.map.baidu.com/lbsapi/getpoint/index.html</p>',
        ),
        array(
          'id'     => 'dzmc',
          'type'   => 'text',
          'title' => '公司名称',
          'attributes' => array(
            'style'    => 'width: 100%;'
          ),
          'after'  => '<p class="cs-text-muted">输入公司名称</p>',
        ),
        array(
          'id'     => 'dtxxms',
          'type'   => 'textarea',
          'title' => '详细描述',
          'attributes' => array(
            'style'    => 'width: 100%;'
          ),
          'after'  => '<p class="cs-text-muted">输入公司详细描述</p>',
        ),

	),

) );




