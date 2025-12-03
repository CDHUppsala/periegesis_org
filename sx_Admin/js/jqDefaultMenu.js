var $sx_menu = jQuery.noConflict();
$sx_menu(document).ready(function () {
	$sx_menu("#menuBG h2 span").click(function () {
		var sxThis = $sx_menu(this);
		var sxParent = sxThis.parent();
		sxThis.toggleClass("selected")
		sxParent.siblings("h2").find("span").removeClass("selected");
		sxParent.next("div").slideToggle(400).siblings("div").slideUp(300);
	});
	$sx_menu("#menuBG h2 a").click(function () {
		var thisLink = $sx_menu(this);
		var sxParent = thisLink.parent();
		thisLink.siblings().toggleClass("selected");
		sxParent.siblings("h2").find("span").removeClass("selected");
		sxParent.next("div").slideToggle(400).siblings("div").slideUp(300);
	});

	var sx_content_width = $sx_menu("#aside").css("width");
	var sx_main_left = $sx_menu("#main").css("left");
	$sx_menu("#jqToggleContent").click(function () {
		var $this = $sx_menu(this);
		var $layerHide = $sx_menu("#aside");
		var $layerShow = $sx_menu("#main");
		if ($this.attr("class") == "aside_show") {
			$layerHide.animate({
				"width": "0px"
			}, 400);
			$layerShow.animate({
				"left": "20px"
			}, 400);
			$this.removeClass("aside_show").addClass("aside_hide");
		} else {
			$layerHide.animate({
				"width": sx_content_width
			}, 400);
			$layerShow.animate({
				"left": sx_main_left
			}, 400);
			$this.removeClass("aside_hide").addClass("aside_show");
		}
	});

});