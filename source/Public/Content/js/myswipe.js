// JavaScript Document

// pure JS
 $(function(){
  	var pic_length=$('.swipe-wrap .sw-c').length;
	 for(i=0; i<pic_length; i++){
		 $('.swipe_num').append('<b></b>');
		 }	
	 $('.swipe_num b').eq(0).addClass("on");
	 })		
var elem = document.getElementById('mySwipe');
window.mySwipe = Swipe(elem, {
startSlide: 0,  //起始图片切换的索引位置
auto: 3000, //设置自动切换时间，单位毫秒
continuous: true,  //无限循环的图片切换效果
//disableScroll: false,  //阻止由于触摸而滚动屏幕
//stopPropagation: true,  //停止滑动事件
//callback: function(index, element) {},  //回调函数，切换时触发
  callback: function(index) {
	$('.swipe_num b').eq(index).addClass("on").siblings().removeClass("on");
	  }
});
//transitionEnd: function(index, element) {}  //回调函数，切换结束调用该函数。

// with jQuery
// window.mySwipe = $('#mySwipe').Swipe().data('Swipe');