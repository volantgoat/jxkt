// 留言提交成功后 倒计时
var t = 60;    
function showTime(item){

    item ? item : item = '#submit_message';

    t -= 1;  
    $(item).text('提交成功（'+t+'）');
    $('#submit_message').css("pointer-events","none").css("background-color","#4CAF50").css("border","1px solid #4caf4f").css("color","#fff")
    var f = setTimeout("showTime('"+item+"')",1000); 

    if(t==0){
        window.location.reload(); //刷新当前页面
        //$(item).text('提交留言');
        //$('#submit_message').css("pointer-events","auto")
        //window.clearTimeout(f);
        t=60;
    } 

}

(function( $ ){
$(document).ready(function() {
	//幻灯片
	/*$(window).load(function() {
		$("#responsive-309391").carouFredSel({
			responsive: true,
			width		: '100%',
			items		: { visible: 1 },
			auto 	  	: { pauseOnHover: true, timeoutDuration:5000 },
			swipe    	: { onTouch:true, onMouse:true },
			pagination	: "#carousel-page-309391",
			prev 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-prev"); }},
			next 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-next"); }}
		});
		$("#responsive-309391 .carousel-item").show()
	});*/
	
	//幻灯片
	$(function(){
	    $('#responsive-309391').owlCarousel({
			items: 1,
	        itemsDesktop : [1000,1],
	        itemsDesktopSmall : [900,1],
	        itemsTablet: [600,1],
	        itemsMobile : [479,1],
	        autoPlay:true,
			pagination:true,
			navigation: true,
			navigationText: ["<i class='la la-angle-left'></i>","<i class='la la-angle-right'></i>"]
	    });
	});

	//产品页面
	$(function(){
	    $('#produc-slider').owlCarousel({
			items: 1,
			autoPlay:3000,
			pagination:true,
	        autoHeight: true,
			navigation: false,
	    });
	});

	//菜单
	$(document).ready(function() {
		$(".touch-toggle a").click(function(event) {
			var className = $(this).attr("data-drawer");
			if ($("." + className).css('display') == 'none') {
				$("." + className).slideDown().siblings(".drawer-section").slideUp()
			} else {
				$(".drawer-section").slideUp()
			}
			event.stopPropagation()
		});
		$('.touch-menu a').click(function() {
			if ($(this).next().is('ul')) {
				if ($(this).next('ul').css('display') == 'none') {
					$(this).next('ul').slideDown();
					$(this).find('i').attr("class", "touch-arrow-up")
				} else {
					$(this).next('ul').slideUp();
					$(this).next('ul').find('ul').slideUp();
					$(this).find('i').attr("class", "touch-arrow-down")
				}
			}
		})
	});

	$('.touch-menu .menu-item-has-children>a').attr('href','javascript:;');

	//问答滚动列表
	$(function() {
		$("#rolling_demo_onePage268793").carouFredSel({
			height: 340,
			auto: {
				duration: 8000,
				easing: "linear",
				pauseDuration: 1
			},
			scroll: {
				easing: 'linear',
				pauseOnHover: 'immediate'
			}
		}).trigger("configuration", ["direction", "up"]).css("width", "100%")
	});

	//内容加载后的运动效果
	if(xintheme.data_animate == 'true'){
		dataAnimate();
	}
	//返回顶部
	function goTop(){
		$(window).scroll(function(e) {
			if($(window).scrollTop()>100)
				$(".gotop").fadeIn(350).css("display","block");
			else
				$(".gotop").fadeOut(350).css("display","none");
		});
			
		$(".gotop").click(function(e) {
			$('body,html').animate({scrollTop:0},500);
			return false;
		});		
	};
	goTop();
	
	//scrollable-default
	$(".scrollable-default").carouFredSel({
		width   	: '100%',
		infinite 	: false,
		//circular 	: false,
		auto 	  	: { pauseOnHover: true, timeoutDuration:3500 },
		swipe    	: { onTouch:true, onMouse:true },
		prev 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-prev"); }},
		next 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-next"); }}
	});
	$(".scrollable-default").parents(".scrollable").css("overflow","visible");
	//产品轮播
	$(".full-scrollable-default").carouFredSel({
		infinite 	: false,
		circular 	: false,
		auto 		: false,
		swipe    	: { onTouch:true, onMouse:true },
		responsive	: true,
		items		: {
			visible		: {
				min			: 2,
				max			: 8
			}
		},
		prev 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-prev"); }},
		next 		: { button:function() { return $(this).parent().next('.carousel-direction').find(".carousel-next"); }}								
	});	
	//重置高度
	$(".full-scrollable-default").parents('.caroufredsel_wrapper').css({
		'height': ($(".full-scrollable-default").find('li').outerHeight()) + 'px'
	});
	$('.popup-show-btn').click(function(){
		$('.popup').show();
		$('.popup-overlay').height($(document).height());
		$('.popup-content').css({marginLeft:-($('.popup-content').outerWidth()/2), marginTop:-($('.popup-content').outerHeight()/2)});
		$('.popup-close-btn').click(function(){
			$(this).parents('.popup').hide();	
		});
		
		return false;
	
	});

	//导航栏下拉固定
	$(window).scroll(function() {
		if ($(window).scrollTop() > 190) {
			$('.header-v4').addClass("sticky");
		} else {
			$('.header-v4').removeClass("sticky");
		}
	});

	//搜索
	// Handle click on toggle search button
	$('#toggle-search').click(function() {
		$('#search-form, #toggle-search').toggleClass('open');
		return false;
	});

	// Handle click on search submit button
	$('#search-form input[type=submit]').click(function() {
		$('#search-form, #toggle-search').toggleClass('open');
		return true;
	});

	// Clicking outside the search form closes it
	$(document).click(function(event) {
		var target = $(event.target);
  
		if (!target.is('#toggle-search') && !target.closest('#search-form').size()) {
			$('#search-form, #toggle-search').removeClass('open');
		}
	});

	//产品中心菜单
	$(function(){
		$(".produc-menu-children .controls li ul.children").parent().addClass("the-cat-parent");

		//手机端 移除二级菜单链接
		var ua = navigator.userAgent;
		var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
		isIphone =!ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
		isAndroid = ua.match(/(Android)\s+([\d.]+)/),
		isMobile = isIphone || isAndroid;

		if(isMobile){
			$('.the-cat-parent>a').attr('href','javascript:;');
		}

	});

    /* QQ */
    jQuery('a.qq-share').click(function (e) {
        e.preventDefault();
        window.open('https://connect.qq.com/widget/shareqq/index.html?url=' + jQuery(this).attr('href') + '&title=' + jQuery(this).attr('data-title') + '&pics=' + jQuery(this).attr('data-image') + '&summary=' + jQuery(this).attr('data-excerpt'), "qqWindow", ["toolbar=0,status=0,resizable=1,width=640,height=560,left=", (screen.width - 640) / 2, ",top=", (screen.height - 560) / 2].join(""));
        return false;
    });

    /* 微信 */
    jQuery('a.weixin-share').click(function (e) {
        e.preventDefault();
        window.open( jQuery(this).attr('href'), "weixinWindow", ["toolbar=0,status=0,resizable=1,width=640,height=560,left=", (screen.width - 640) / 2, ",top=", (screen.height - 560) / 2].join(""));
        return false;
    });

    /* 微博 */
    jQuery('a.weibo-share').click(function (e) {
        e.preventDefault();
        window.open('https://service.weibo.com/share/share.php?url' + jQuery(this).attr('href') + '&type=button&language=zh_cn&title=' + '【' + jQuery(this).attr('data-title') + '】' + jQuery(this).attr('data-excerpt') + '&pic=' + jQuery(this).attr('data-image') + '&searchPic=true', "weiboWindow", ["toolbar=0,status=0,resizable=1,width=640,height=560,left=", (screen.width - 640) / 2, ",top=", (screen.height - 560) / 2].join(""));
        return false;
    });

	/*fancybox*/
	$(function() {
		jQuery(".wp-block-gallery a").attr("data-fancybox","images");
		jQuery(".wp-block-image a").attr("data-fancybox","images");
		jQuery(".gallery-item a").attr("data-fancybox","images");
		//jQuery('a[data-fancybox="images"]').fancybox();
	});

	/*ajax留言*/
    $('#contact-form').submit(function(event) {
        event.preventDefault();

        $.ajax({
            url: dahuzi.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: $('#contact-form').serializeArray(),
        })
        .done(function( data ) {
            if( data != 0 ){
                if( data.state == 200 ){
                    $('#form-messages').removeClass('error').addClass('success').text(data.tips);
                    showTime('#submit_message');
                }else if( data.state == 201 ){
                    $('#form-messages').removeClass('success').addClass('error').text(data.tips);
                }
            }else{
                $('#form-messages').removeClass('success').addClass('error').text('请求错误！');
            }
        })
        .fail(function() {
            alert('网络错误！');
        });

    });
	
});
})( jQuery );

//头部公告
if(xintheme.head_notice == 'true'){
	window.onload = function(){
		//头部公告
		if(getCookie("head-notice")==0){
			document.getElementById("hellobar").style.cssText="margin-top:-56px;";
		}else{
			document.getElementById("hellobar").style.cssText="margin-top:0;";
		}
	}
	//关闭头部公告
	function closeNotice() {
		document.getElementById("hellobar").style.cssText="margin-top:-56px;";
		setCookie("head-notice","0"); 
	}
	//设置cookie 
	function setCookie(name,value){ 
	    var exp = new Date();  
	    //exp.setTime(exp.getTime() + 1*60*60*1000);//有效期1小时
		exp.setTime(exp.getTime() + 30*60*1000); //有效期30分钟
	    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
	}
	//取cookies函数 
	function getCookie(name){ 
	    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)")); 
	    if(arr != null) return unescape(arr[2]); return null; 
	}
}
//头部公告  EDN

//微信弹出层
jQuery(document).ready(function($) {
    $('.mobile_foot_menu_img').on('click', function() {
        $('.mobile-foot-weixin-dropdown').toggleClass('is-visible');
    });

    $('.button_img').on('click', function() {
        $('.button-img-dropdown').toggleClass('is-visible');
    });

	$(".close-weixin").on('click', function() {
        $(".mobile-foot-weixin-dropdown").removeClass('is-visible');
        $(".button-img-dropdown").removeClass('is-visible');
	});
	
});