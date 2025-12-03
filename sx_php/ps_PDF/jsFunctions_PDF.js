var $sx=jQuery.noConflict();
$sx(document).ready(function() {
    if ($sx(".jqAccordionNav div").length) {
        $sx(".jqAccordionNav div").click(function () {
            $sx(this).toggleClass("open")
                .next("ul").slideToggle(400)
                .end()
                .parent()
                .siblings()
                .find("div").removeClass("open")
                .end()
                .find("ul").hide(400);
        });
	}
	//To keep the last clicked link selected while navigating the manu
	$sx('.jqAccordionNav li li a').click(function(){
		$sx('.jqAccordionNav a').removeClass("open");
		$sx(this).addClass("open");
	});

    $sx("#jqNavMarker").click(function(){
		var sxRight = $sx('main .right');
		var sxLeft = $sx('main .left');
		if(sxRight.width() < 32){
			sxLeft.fadeOut(200).animate({right: '100%'}, 400);
			sxRight.animate({left: '0'},400);
		}else{
			sxLeft.animate({right: '0'}, 400).fadeIn(200);
			sxRight.animate({left: '100%'},400);
		};
	});
});

//Delete Loaded files
var sxAjaxDeleteFiles = function(cc) {
	sxData = "sx="+ cc;
	$sx.ajax({
		url: "ajax_Delete.php",
		cache: false,
		data: sxData,
		dataType:"html",
		scriptCharset:"utf-8",
		type:"GET",
		success:function(result){},
		error:function(xhr, status, error) {}
	});
};
