;
(function($) {
    $(document).ready(function() {
        var slider = tns({
            container: '.slider',
            speed: 300,
            items: 1,
            autoplay: true,
            autoplayTimeout: 3000,
            autoHeight: true,
            controls: true,
            nav: true,
            autoplayButtonOutput: true
        });
    });
})(jQuery);