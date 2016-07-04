// JavaScript Document

// JavaScript Document


window.onload = function(){
		var width = document.body.clientWidth;
		var num = 3;
		console.log(width);
		var container = document.getElementById('container');
		var list = document.getElementById('list');
		var buttons = document.getElementById('buttons').getElementsByTagName('span');
		var prev = document.getElementById('prev');
		var next =	document.getElementById('next');
		var imgs = document.getElementsByTagName('img');
		var index = 1;
		var animated = false;
		
		container.style.width = width + 'px';
		list.style.width = width * (num + 2) + 'px';
		list.style.left = -width + 'px';
		for(var i = 0; i < imgs.length; i++){
			imgs[i].style.width = width + 'px';	
		}
		
		function showButton(){
			for(var i = 0; i < buttons.length; i++){
				if(buttons[i].className == 'on'){
					buttons[i].className = '';
					break;	
				}	
			}
			buttons[index - 1].className = 'on';	
		}
		
		function animate(offset){
			animated = true;
			var newLeft = parseInt(list.style.left) + offset;
			var time = 150;//位移总的时间
			var interval = 10;//位移间隔时间
			var speed = offset / (time / interval);//每次位移量
			
			
			function go(){
				if((speed < 0 && parseInt(list.style.left) > newLeft) || (speed > 0 && parseInt(list.style.left) < newLeft)){
					list.style.left = parseInt(list.style.left)	+ speed + 'px';
					setTimeout(go,interval);
				}	
				else{
					animated = false;
					list.style.left = newLeft + 'px';	
					if(newLeft > -width){
						list.style.left = -width * 3 + 'px';	
					}
					if(newLeft < -width * 3){
						list.style.left = -width + 'px';	
					}
				}
			}
			go();
		}
		
		function play(){
			timer = setInterval(function(){
				next.onclick();	
			},2000);	
			prev.style.display = 'none';
			next.style.display = 'none';
		}
		
		function stop(){
			clearInterval(timer);	
			prev.style.display = 'block';
			next.style.display = 'block';
		}
		
		next.onclick = function(){
			if(index == 3){
				index = 1;	
			}
			else{
				index += 1;	
			}
			showButton();
			if(animated == false){
				animate(-width);	
			}
		}
		prev.onclick = function(){
			if(index == 1){
				index = 3;	
			}
			else{
				index -= 1;	
			}
			showButton();
			if(animated == false){
				animate(width);	
			}
		}
		
		for(var i = 0; i < buttons.length; i++){
			buttons[i].onclick = function(){
				if(this.className == 'on')
				{
					return;
				}
				var myIndex = parseInt(this.getAttribute('index'));
				var offset = -width * (myIndex - index);
				
				if(animated == false){
					animate(offset);	
				}
				index = myIndex	;
				showButton();
			}	
		}
		
		container.onmouseover = stop;
		container.onmouseout = play;
		
		play();
	}