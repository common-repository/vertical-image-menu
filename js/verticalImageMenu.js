/*
---
description: Vertical Image Menu JScripts
authors:
- Benedikt Morschheuser (http://software.bmo-design.de) 2012
requires:
- jQuery
*/

(function($) {
	var speed = 20;//config
	if(vimSpeed)
		speed=vimSpeed;
	
	var scrollLength = 0;
	var scrollPosition = 0;
	var duration = 1;
	
	$(document).ready(function(){
		//mouseover events
		$('.vertical_imagemenu').hover(function() {
			$('.vertical_imagemenu').stop();
		}, 
		function() {
			startScrolling();
		})
		
		//clone content
		$('.vertical_imagemenu table').clone().appendTo('.vertical_imagemenu');
		
		//css
		$('.vertical_imagemenu').bind('mousewheel', function(event, delta) {
         	return false;//ausschalten des mousewheels
     	}).css("overflow","hidden");
		
		scrollReset();
		
	});
	function startScrolling(){
		//scrollLength=$('.vertical_imagemenu table').height()-$('.vertical_imagemenu').height(); (normal, hier wegen clone ohne)
		scrollLength=$('.vertical_imagemenu table').height();
		scrollPosition = $('.vertical_imagemenu').scrollTop();
		duration =  speed*(scrollLength-scrollPosition); 
		$('.vertical_imagemenu').scrollTo({top:scrollLength+'px', left:'0px'}, duration,  {axis:'y',easing: 'linear',onAfter: scrollReset});
	}
	function scrollReset(){
		//reset
		$('.vertical_imagemenu').scrollTo({top:'0px', left:'0px'}, 1,  {axis:'y',easing: 'linear',onAfter: function(){
			//start Animation
			startScrolling();
		}});
	}
})(jQuery); 



