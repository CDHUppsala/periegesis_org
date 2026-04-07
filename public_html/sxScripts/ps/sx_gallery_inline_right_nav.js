/*
	Responsive inline-block gallery with Full Screen View
	Public Sphere
==================================== */

var $sx = jQuery.noConflict();
$sx(function () {
	if ($sx('.jqps_inline_gallery').length) {
		var sxGallery = $sx('.jqps_inline_gallery');
		var sxLength = sxGallery.find('figure').length
		sx_startInlineGallery(sxGallery, sxLength)
	};
	// Click on any image both starts and closes the gallery
	$sx('#widescreen').on('click', function () {
		$sx('.jqps_inline_gallery figure:first-child').find('img').click();
	})
});

var sx_startInlineGallery = function (sxGallery, sxLength) {
	sxGallery.find("img").click(function () {
		// Close the gallery if it is open, else open it
		if ($sx('#sxFixedScreen').length) {
			$sx('#sxFixedScreen').fadeTo(300, 0, function () {
				$sx('html body').css("overflow", "auto");
				$sx('#sxFixedScreen').remove();
			});
			//Redefine the original element as new Gallery object
			sxGallery = null;
			sxGallery = $sx('.jqps_inline_gallery');
		} else {
			// Move from image to the parent figure
			var sxThis = $sx(this).parent()
			sxThis.addClass('active').siblings().removeClass('active');
			var sxIndex = sxThis.index()
			var sxNotes = '';
			if (sxThis.find('figcaption').length) {
				sxNotes = sxThis.find('figcaption').html();
			}

			$sx('body').append('<div id="sxFixedScreen"></div>');
			var sx_FixedScreen = $sx('#sxFixedScreen');

			sx_FixedScreen.append($sx('.jqps_inline_gallery').clone(true, true));
			/*	Redefine the cloned element as new Gallery object
				and replace default styling classes with 
			*/
			sxGallery = null;
			sxGallery = $sx('#sxFixedScreen .jqps_inline_gallery');
			sxGallery
				.removeClass('photo_grid').addClass('ps_inline_gallery_absolute')
				.append('<ul class="nav"></ul>')
				.find('figure').fadeOut(0)
				.end()
				.find('.nav')
				.append('<li class="nav-prev"></li><li class="nav-close"></li><li class="nav-next"></li>')
				.append('</li><li class="notes"></li>')
				.find('.notes')
				.html('image ' + (sxIndex) + ' / ' + sxLength + '<br>' + sxNotes);

			if (sxGallery.find('figure figcaption').length) {
				sxGallery.find('figure figcaption').css('display', 'none');
			}
			sx_FixedScreen.fadeTo(300, 1, function () {
				$sx('html body').css("overflow", "hidden");
				sxGallery.find('figure.active').fadeIn(300);
			});

			function cycleImages(pos) {
				var $active = sxGallery.find('figure.active');
				if (pos == 'prev') {
					var $next = ($active.prev('figure').length > 0) ? $active.prev('figure') : sxGallery.find('figure:last');
				} else if (pos == 'next') {
					var $next = ($active.next('figure').length > 0) ? $active.next('figure') : sxGallery.find('figure:first');
				} else { // Close the gallery
					sxGallery.find('figure.active').find("img").click();
				};
				sxGallery.find('.nav').fadeOut(300);
				$active.fadeOut(300, function () {
					sxNotes = '';
					if ($next.find('figcaption').length) {
						sxNotes = $next.find('figcaption').html();
					}
					sxGallery.find('.nav')
						.find('.notes').html('image ' + ($next.index()) + ' / ' + sxLength + '<br>' + sxNotes)
						.end()
						.fadeIn(300)
					$active.removeClass('active');
					$next.addClass('active').fadeIn(300);
				});
			};

			$sx("body").keydown(function (e) {
				if (e.keyCode == 37) {
					cycleImages('prev');
				} else if (e.keyCode == 39) {
					cycleImages('next');
				} else if ((e.keyCode == 38 || e.keyCode == 40) && $sx('#sxFixedScreen').length) {
					sxGallery.find('figure.active').find("img").click();
				}
			});

			sxGallery.find("[class*='nav-']").click(function () {
				if ($sx(this).hasClass('nav-prev')) {
					cycleImages('prev');
				} else if ($sx(this).hasClass('nav-next')) {
					cycleImages('next');
				} else {
					cycleImages('close');
				}
			});

		};
	});
};