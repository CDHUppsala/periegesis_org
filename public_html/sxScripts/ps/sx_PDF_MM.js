var $sx = jQuery.noConflict();
$sx(document).ready(function () {
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
	$sx('.jqAccordionNav li li a').click(function () {
		$sx('.jqAccordionNav a').removeClass("open");
		$sx(this).addClass("open");
	});

	var sx_main_right = 100 - ($sx('main').width() / $sx(window).width() * 100).toFixed(1);
	var sx_aside_left = 100 - sx_main_right;
	var sx_loop_less = false;
	var sx_loop_more = false;
	$sx("#jqNavMarker").click(function () {
		var main = $sx('main');
		var aside = $sx('aside');

		if ($sx(window).width() < 1280) {
			main.addClass('main_importent');
			aside.addClass('aside_importent');
			main.removeClass('main_min_importent');
			aside.removeClass('aside_min_importent');

			sx_loop_less = true;
			main.css({
				'z-index': 1,
				'right': 0
			});
			if(sx_loop_more == true) {
				aside.css('left', '100%')
				sx_loop_more = false;
			}
			if (aside.width() < 32) {
				aside.css('z-index', 100).stop().animate({
					left: '0%'
				}, 400);
			} else {
				aside.stop().animate({
					left: '100%'
				}, 400, function () {
					aside.css({'z-index': 1});
				});
			};
		} else {
			main.removeClass('main_importent');
			aside.removeClass('aside_importent');
			main.addClass('main_min_importent');
			aside.addClass('aside_min_importent');

			sx_loop_more = true;
			aside.css({
				'z-index': 1,
				'left': sx_aside_left +'%'
			});
			if(sx_loop_less == true) {
				main.css('right', sx_main_right + '%');
				sx_loop_less = false;
			}
			if ((main.width() + 32) < $sx(window).width()) {
				main.css('z-index', 100).stop().animate({
					right: '0%'
				}, 400);
			} else {
				main.stop().animate({
					right: sx_main_right + '%'
				}, 400, function () {
					main.css('z-index', 1);
				});
			}
		}
	});


	$sx(".jqTabLinks li").click(function () {
		$sx(this).addClass("active").siblings().removeClass("active");
		var sxNextUL = $sx(this).parent().next("ul");
		var sxThisLI = sxNextUL.find("li").eq($sx(this).index());
		sxThisLI.siblings().slideUp("slow", function () {
			sxThisLI.slideDown("slow");
		});
	});
});