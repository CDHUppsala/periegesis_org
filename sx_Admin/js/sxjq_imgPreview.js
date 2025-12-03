var $sx_ipv = jQuery.noConflict();
$sx_ipv(document).ready(function(){
	var intX = (screen.width);
	var intY = (screen.height);
	var radioL;
	var iTop;
	var maxW, maxH;
	$sx_ipv("img.imgPreview").hover(function(f){
		var $this= $sx_ipv(this);
		var imgW = $this.width();

		radioL = (f.pageX <= (intX/2)) ? true : false;
		iTop= (f.screenY <= ((intY/2)+100)) ? 120 : 250;
		maxW= Math.round((intX/2) - (imgW*1.1))

		var t = this.title;
		var curt = (t != "") ? "<br/>" + t : "";
		var imgURL= this.href;
		if(!imgURL){imgURL= this.src};
		$sx_ipv("body").delay(300).append("<div id='imgPreview'><img src='"+ imgURL +"' />"+ curt +"</div>");

		var imgDiv= $sx_ipv("#imgPreview");
		if(radioL){
			imgDiv.css("left",(f.pageX + 20) + "px")
		}else{
			imgDiv.css("right",((intX-f.pageX) + 20) + "px")
		}
		imgDiv.css("top",((f.pageY - f.screenY) + iTop) + "px")
			.css("maxWidth",maxW +"px")
			.fadeIn("fast");
	}, function(){$sx_ipv("#imgPreview").stop(true,true).remove();});

	$sx_ipv("img.imgPreview").mousemove(function(f){
		var imgDiv= $sx_ipv("#imgPreview");
		if(radioL){
			imgDiv.css("left",(f.pageX + 20) + "px")
		}else{
			imgDiv.css("right",((intX-f.pageX) + 20) + "px")
		};
		imgDiv.css("top",((f.pageY - f.screenY) + iTop) + "px")
			.css("maxWidth",maxW +"px")
	});			
});