const $ = jQuery;

$(function () {
    const carousel = $('#album-carousel');

    $(document).bind('keyup', function(e) {
        if(e.which === 39){
            carousel.carousel('next');
        }
        else if(e.which === 37){
            carousel.carousel('prev');
        }
    });

    $('.thumbnail').click(function () {
        const key = $(this).data('key');

        carousel.find('.carousel-item').removeClass('active');
        $('#photo-' + key).addClass('active');

        $('#album').modal();
        carousel.carousel({
            keyboard: true,
        });
    });
});
