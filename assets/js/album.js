const $ = jQuery;

$(function () {
    $('.thumbnail').click(function () {
        const key = $(this).data('key');

        const carousel = $('#album-carousel');

        carousel.find('.item').removeClass('active');
        $('#photo-' + key).addClass('active');

        $('#album').modal();
        carousel.carousel();
    });
});
