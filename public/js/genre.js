

$(function () {
    $(".template_list").iziModal({
        top: 0,
        width: "420px",
        radius: "25px 25px 0 0",
        transitionIn: 'fadeInUp',
        transitionOut: 'fadeOutDown',
        onClosed: function () {
            $(".template_list .iziModal-content").empty();
            $(".genre_area").css("z-index", '');
            $('body').removeAttr('style');
            $(".speaker_list").iziModal('open');
        },
        onOpening: function (modal) {
            $("#fakeLoader").fakeLoader('start');
            $.ajax({
                url: '/talk/template_list',
                type: 'get',
                data: {
                    'template_id': $("#template_id").val(),
                },
                dataType: 'json',
            }).then(
                function (data) {
                    if (data.errors) {
                        $("#fakeLoader").fakeLoader('stop');
                        $(".template_list").iziModal('close');
                        toastr.error(data.errors);
                        return false;
                    }
                    
                    $(".template_list .iziModal-content").append(data.result);
                    $(".genre_area").css("z-index", 1000);
                    $("#fakeLoader").fakeLoader('stop');
                },
                function () {
                    $("#fakeLoader").fakeLoader('stop');
                    $(".template_list").iziModal('close');
                    toastr.error('GENRE-001 : エラーが発生しました。');
                })
        }
    })

    $(document).on('click', '#template_change', function (event) {
        event.preventDefault();
        $('html').animate({scrollTop: 0}, 600);
        $('body').animate({scrollTop: 0}, 600);
        $(".template_list").iziModal('open');
        $(".speaker_list").iziModal('close');
    });
})
