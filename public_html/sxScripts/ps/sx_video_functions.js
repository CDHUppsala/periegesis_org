$sx(function () {
    var video = document.getElementById('video_desktop');
    if (window.getComputedStyle(video).display == 'none') {
        video = document.getElementById('video_mobile');
    }
    $sx('.play').on('click', function () {
        video.play();
    });
    $sx('.pause').on('click', function () {
        video.pause();
    });
    $sx('.replay').on('click', function () {
        video.pause();
        video.currentTime = 0;
        video.play();
    });
    $sx('.fullscreen').on('click', function () {
        video.requestFullscreen();
        video.play();
    });
})
