$(function () {
    $(document).on('click', '#back_button_header_top', function (event) {
        message_dialog('#message_conf_talk', 'body');
    });

    $(document).on('click', '#back_button_footer_top', function (event) {
        message_dialog('#message_conf_top', '.footer_modal');
    });

    $(document).on('click', '#back_button_footer_talk', function (event) {
        message_dialog_ajax('#message_conf_talk', '.footer_modal');
    });

    $(".twitter_message").iziModal({
        width: "400px",
        radius: "10px",
        onClosed: function () {
            $(".iziModal-content").empty();
            $('body').removeAttr('style');
        },
        onOpening: function (modal) {
            var default_content =  $("#default_content_movie").val();
            let html = '<div id="twitter_area">\n' +
                '<textarea class="area_twitter" rows="5" placeholder="メッセージを入力" id="content">\n' +
                '</textarea>\n' +
                '    <i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i>\n' +
                '    <a href="#" class="btn_twitter_send" id="send_twitter">twitterに投稿</a>\n' +
                '</div>'
            $(".twitter_message .iziModal-content").append(html);
            $(".area_twitter").val(default_content);
        }
    })

    $(document).on('click', '#twitter_message_movie', function (event) {
        event.preventDefault();
        $("#send_type").val('movie');
        $(".twitter_message").iziModal('open');
    });

    // $(document).on('click', '#twitter_message_screenshot', function (event) {
    //     event.preventDefault();
    //     $("#send_type").val('screenshot');
    //     $(".twitter_message").iziModal('open');
    // });

    $(document).on('click', '.btn_twitter_send', function (event) {
        $("#fakeLoader").fakeLoader('start');
        $('#twitter_content').val($(".area_twitter").val());
        $('#twitter_send').submit();
        $(".twitter_message").iziModal('close');
    });

    $(".mode_list").iziModal({
        width: "420px",
        radius: "25px 25px 0 0",
        bottom: 0,
        transitionIn: 'fadeInUp',
        transitionOut: 'fadeOutDown',
        onClosed: function () {
            $(".mode_list .iziModal-content").empty();
            $('body').removeAttr('style');
        },
        onOpening: function (modal) {
            $.ajax({
                url: '/movie/mode_list',
                type: 'get',
                data: {
                    'mode_id': $("#mode_id").val(),
                    'color_id': $("#color_id").val(),
                },
                dataType: 'json',
            }).then(
                function (data) {
                    if (data.errors) {
                        $("#fakeLoader").fakeLoader('stop');
                        $(".mode_list").iziModal('close');
                        toastr.error(data.errors);
                        return false;
                    }
                    $(".mode_list .iziModal-content").append(data.result);
                    $("#fakeLoader").fakeLoader('stop');
                },
                function (error) {
                    errors(error.status);
                    return false;
                })
        }
    })

    $(document).on('click', '#mode_change', function (event) {
        event.preventDefault();
        $("#fakeLoader").fakeLoader('start');
        $(".mode_list").iziModal('open');
    });

    $(document).on('click', '#mode_box > li', function (event) {
        var mode_id = $(this).data('mode-id');
        1 != mode_id ? $("#color_box").addClass('fixed') : $("#color_box").removeClass('fixed');
        $(this).parent().find('li .selected').removeClass('selected');
        $(this).parent().find('li .check').remove();
        $(this).children('img').addClass('selected');
        $(this).append('<div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>');
        selected_bottom();
    });

    $(document).on('click', '#color_box > li', function (event) {
        var mode_id = $("#mode_box").find('li .selected').parent().data('mode-id');
        if(99 === $(this).data('color-id') || 1 != mode_id){
            return false;
        }
        $(this).parent().find('li .selected').removeClass('selected')
        $(this).parent().find('li .check').remove();
        $(this).children('.color_img').addClass('selected');
        $(this).append('<div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>');
        selected_bottom();
    });

    $(document).on('click', '#mode_selected', function (event) {
        var mode_id = $("#mode_box").find('li .selected').parent().data('mode-id');
        var color_id = $("#color_box").find('li .selected').parent().data('color-id');
        var fd = '';
        if("99" === color_id){
            fd = new FormData();
            if ($("input[name='upload_photo']").val()!== '') {
                fd.append( "file", $("input[name='upload_photo']").prop("files")[0] );
            }
        }
        if(mode_id == $("#mode_id").val() && color_id == $("#color_id").val()){
            $(".mode_list").iziModal('close');
            return;
        }
        $("#mode_id").val(mode_id);
        $("#color_id").val(color_id);
        $("#fakeLoader").fakeLoader('start');
        $("#movie_create").submit();
    });

    $(document).on('change','input[name="upload_photo"]',function(){
        var fd = new FormData();
        if ($("input[name='upload_photo']").val()!== '') {
            fd.append( "file", $("input[name='upload_photo']").prop("files")[0] );
        }
        $("#color_box").find('li .selected').removeClass('selected');
        $("#color_box").find('li .check').remove();
        $($("#color_box").find('li')[0]).children('.color_img').addClass('selected');
        $($("#color_box").find('li')[0]).append('<div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>');
        $("#mode_id").val(1);
        $("#color_id").val(99);
        $(".mode_list").iziModal('close');
        $("#fakeLoader").fakeLoader('start');
        $("#movie_create").submit();
    });

    $(document).on('click', '#input_photo', function (event) {
        var mode_id = $("#mode_box").find('li .selected').parent().data('mode-id');
        if(1 != mode_id){
            return false;
        }
        $("#upload_photo").click();
        return false; // must!
    });

    $(".title_modal").iziModal({
        width: "420px",
        radius: "25px 25px 0 0",
        top: 150,
        transitionIn: 'fadeInUp',
        transitionOut: 'fadeOutDown',
        onClosed: function () {
            $(".title_modal .iziModal-content").empty();
            $('body').removeAttr('style');
        },
        onOpening: function (modal) {
            $.ajax({
                url: '/movie/title',
                type: 'get',
                dataType: 'json',
            }).then(
                function (data) {
                    if (data.errors) {
                        $("#fakeLoader").fakeLoader('stop');
                        $(".title_modal").iziModal('close');
                        toastr.error(data.errors);
                        return false;
                    }
                    $(".title_modal .iziModal-content").append(data.result);
                    $("#fakeLoader").fakeLoader('stop');
                },
                function (error) {
                    errors(error.status);
                    return false;
                })
        }
    })

    $(document).on('click', '.movie_title_area', function (event) {
        event.preventDefault();
        $("#fakeLoader").fakeLoader('start');
        $(".title_modal").iziModal('open');
    });

    $(document).on('click', '#title_selected', function (event) {
        $("#title").val($(".title").val());
        $("#fakeLoader").fakeLoader('start');
        $("#movie_create").submit();
    });
})

function selected_bottom() {
    var mode_id = $("#mode_box").find('li .selected').parent().data('mode-id');
    var color_id = $("#color_box").find('li .selected').parent().data('color-id');
    if(mode_id == $("#bf_mode").val() && color_id == $("#bf_color").val()){
        $("#mode_selected").hide();
    }
    else{
        $("#mode_selected").show();
    }
    return;
}
function message_dialog(id)
{
    var message = '#message_conf_top' === id ? 'みんなの投稿画面に戻ります。' : 'トークの再編集に戻ります。';
    message += '\nよろしいですか？';
    var locate = '#message_conf_top' === id ? '/' : '/talk';
    if (!confirm(message)) {
        return false;
    } else {
        window.location.href = locate;
    }
}

function message_dialog_ajax(id)
{
    var message = '新しいトークの作成に移動します。\nよろしいですか？';
    if (!confirm(message)) {
        return false;
    } else {
        $.ajax({
            url: '/movie/reset',
            type: 'post',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then(
            function (data) {
                window.location.href = '/talk';
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }
}

function errors(code){
    $("#fakeLoader").fakeLoader('stop');
    window.location.href = '/error/'+code;
}
