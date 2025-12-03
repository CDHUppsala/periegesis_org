var $sx=jQuery.noConflict();
$sx(document).ready(function(){

    $sx("#configurForm").submit(function(e) {
        e.preventDefault();
        var sxURL = $sx(this).attr("data-url")
        $sx.ajax({
            type: "POST",
			cache: false,
            url: sxURL,
            data: $sx(this).serialize(),
			dataType: "html",
			scriptCharset: "utf-8",
	        success: function(result) {
                alert("Saving was successful");
            },
            error: function() {
                alert("Saving was unsuccessful!");
            }
         });
    });

	$sx('#tabBG a').click(function(){
		var $this = $sx(this);
		var $thisID= $this.attr('data-id');
		if($this.attr("class") != "selected"){
			$sx("#tabBG a").removeClass("selected");
			$sx(".tabLayers").hide(500);
			$this.addClass("selected");
			$sx("#"+ $thisID).show(500);
		}
	});

	$sx("#menuBG h2 span").click(function () {
		var sxThis = $sx(this);
		var sxParent = sxThis.parent();
		sxThis.toggleClass("selected")
		sxParent.siblings("h2").find("span").removeClass("selected");
		sxParent.next("div").slideToggle(400).siblings("div").slideUp(300);
	});
	$sx("#menuBG h2 a").click(function () {
		var thisLink = $sx(this);
		var sxParent = thisLink.parent();
		thisLink.siblings().toggleClass("selected");
		sxParent.siblings("h2").find("span").removeClass("selected");
		sxParent.next("div").slideToggle(400).siblings("div").slideUp(300);
	});

	var sx_content_width = $sx("#aside").css("width");
	var sx_main_left = $sx("#main").css("left");
	$sx("#jqToggleContent").click(function () {
		var $this = $sx(this);
		var $layerHide = $sx("#aside");
		var $layerShow = $sx("#main");
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
