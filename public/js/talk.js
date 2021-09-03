
window.onpageshow = function(event) {
    
    if (event.persisted) {
        $(".create_movie").hide();
        window.location.reload();
    }

};

var currentActiveindex = 0;
var generating = true;
var position=[".position01", ".position23",".position45",".position67"];
var mb=["mb-25","mb-30","mb-10",""];
var responses=[];
var backtop = true;
var imgcount = 0;
var readimgcount = 0;
var timeAvatarToballoon = 1000;
var timeCharacters = 75;
var timeCharactertoAvatar = 500;
var timeeraseImage = 1500;
var slider = "";

function appHeight() {
    const doc = document.documentElement
    doc.style.setProperty('--app-height', `${window.innerHeight}px`)
};
document.addEventListener("DOMContentLoaded", function(event) {    
    if(talk_list)
    {
        responses = talk_list.responses
       
        responses.forEach(response=>{
            if(response.content_type=="image")
                imgcount++;
        })
        if(imgcount==0)
        {
            generateTalk(responses);
        }
        responses.forEach(response=>{
            if(response.content_type=="image")
            {
                var img = new Image();
                img.onload = function() {
                    readimgcount++;
                    if(imgcount===readimgcount)
                    {
                        generateTalk(responses);
                    }
                }
                img.src = response.content;
            }
    
        })
        $("#background").val(talk_list.status.background_url);
        document.getElementById("background").style.backgroundImage = `url('${$("#background").val()}')`;
    }

    localStorage.setItem("talkList", JSON.stringify(responses));
})

window.addEventListener('resize', appHeight)
appHeight()

$(function () {
    // speaker
    if($('.speaker_list').length) {
        $(".speaker_list").iziModal({
            width: "420px",
            radius: "25px 25px 0 0",
            overlayClose : false,
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            zindex: 1000,
            onClosed: function () {
                $(".speaker_list .iziModal-content").empty();
                $(".speaker_list .iziModal-content").css('height', '');
                $('body').removeAttr('style');
            },
            onOpening: function (modal) {
                $('body').animate({scrollTop: 0}, 600);
                var init_flg = $("#init_flg").val();
                var position = $("#position").val();
                var img_id = $("#img_id").val();
                $("#fakeLoader").fakeLoader('start');
                $.ajax({
                    url: '/speaker_list',
                    type: 'POST',
                    dataType: 'json',
                    data: {'init_flg': init_flg, 'img_id': img_id, 'position' : position},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                }).then(
                    function (data) {
                        if (data.errors.length) {
                            $("#fakeLoader").fakeLoader('stop');
                            $(".speaker_list").iziModal('close');
                            toastr.error(data.errors);
                            return false;
                        }
                        $(".speaker_list .iziModal-content").append(data.speaker_list);
                       
                        if(document.getElementById("speaker-change"))
                        {
                            document.getElementById("speaker-change").style.backgroundImage = `url('${$("#background").val()}')`;  
                        }
                        $(".speaker_list .genre_name").text( $("#template_name").text());
                        $("#fakeLoader").fakeLoader('stop');
                    },
                    function (error) {
                        $(".speaker_list").iziModal('close');
                        errors(error.status);
                        return false;
                    })
            }
        });
    }

    $(document).on('click', '#changeavatar', function (event) {
        backtop = false;
        event.preventDefault();
        $('html').animate({scrollTop: 0}, 600);
        $('body').animate({scrollTop: 0}, 600);
        $("#position").val(responses[currentActiveindex].turn);
        $(".speaker_list").iziModal('open');
    });

    $(document).on('click', '#backtop', function (event) {
        event.preventDefault();
        if(backtop){
            window.location.href = "/";
        }
        else
        {
            $(".speaker_list").iziModal('close');
        }
    });

    $(document).on('click', '#newtalk', function (event){
        backtop = false;
        $("#init_flg").val(1);
        $(".speaker_list").iziModal('open');
    })

    if(0 != $("#init_flg").val()){
        $(".speaker_list").iziModal('open');
    }

    // filter
    if($('.speaker_filter_modal').length) {
        $(".speaker_filter_modal").iziModal({
            top: "100px",
            width: "320px",
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            zindex: 2000,
            radius: "20px",
            onClosed: function () {
                $(".speaker_filter_modal .iziModal-content").empty();
                $('body').removeAttr('style');
            },
            onOpening: function (modal) {
                $('body').animate({scrollTop: 0}, 600);
                $("#fakeLoader").fakeLoader('start');
                $.ajax({
                    url: '/speaker_categories',
                    type: 'POST',
                    dataType: 'json',
                    data: {'category_id': $("#category_id").val()},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                }).then(
                    function (data) {
                        if (data.errors.length) {
                            $("#fakeLoader").fakeLoader('stop');
                            $(".speaker_filter_modal").iziModal('close');
                            toastr.error(data.errors);
                            return false;
                        }
                        $(".speaker_filter_modal .iziModal-content").append(data.categories);
                        $("#fakeLoader").fakeLoader('stop');
                    },
                    function (error) {
                        $(".speaker_filter_modal").iziModal('close');
                        errors(error.status);
                        return false;
                    })
            }
        });
    }

    $(document).on('click', '#speaker_filter', function (event) {
        event.preventDefault();
        $(".speaker_filter_modal").iziModal('open');
    });

    $(document).on('click', '.filter_cancel', function (event) {
        speaker_filter(0);
    });

    $(document).on('click', '#category_list li', function (e) {
        var category_id = $(this).data('category-id');
        speaker_filter(category_id);
        return true;
    });

    function speaker_filter(category_id){
        $("#category_id").val(category_id);
        $(".speaker_filter_modal").iziModal('close');
        $("#fakeLoader").fakeLoader('start');
        var init_flg = $("#init_flg").val();
        var position = $("#position").val();
        var img_id = 0;
        $.ajax({
            url: '/speaker_filter',
            type: 'POST',
            dataType: 'json',
            data: {'category_id': category_id, 'init_flg': init_flg, 'img_id': img_id, 'position' : position},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then(
            function (data) {
                if (data.errors.length) {
                    $(".speaker_filter_modal").iziModal('close');
                    $("#fakeLoader").fakeLoader('stop');
                    toastr.error(data.errors);
                    return false;
                }
                $(".change_area").empty();
                $(".change_area").append(data.speaker_list);
                $(".speaker_list .iziModal-content").css('height', "100vh");
                $("#fakeLoader").fakeLoader('stop');
            },
            function (error) {
                $(".speaker_filter_modal").iziModal('close');
                errors(error.status);
                return false;
            })
    }

    $(document).on('click', '#images li', function (e) {
        $("#images li").off('click');
        const index = $(this).index();
        if (0 !== index) {
            $("#fakeLoader").fakeLoader('start');
            changePattern($(this).children().attr("id"),$(this).children().attr("src"));
        }
        return true;
    });

    $(document).on('change','input[name="file_photo"]',function(event){
        event.preventDefault();
        $(".resize_image").iziModal('open');
    });

    var $uploadCrop;
    if($('.resize_image').length) {
        $(".resize_image").iziModal({
            top: "100px",
            width: "340px",
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            zindex: 3000,
            radius: "20px",
            onClosed: function () {
                $("#upload-demo").empty();
                $("#upload-demo").removeClass();
                $("input[name='file_photo']").val("");
            },
            onOpening: function (modal) {
                var viewportWidth = 170;
                var viewportHeight = 170;
                var boundaryWidth = 320;
                var boundaryHeight = 210;
                if ($("input[name='file_photo']").prop("files")[0]) {
                    $uploadCrop = $('#upload-demo').croppie({
                        viewport: {
                            width: viewportWidth,
                            height:viewportHeight,
                            type:'square' //circle
                        },
                        boundary: {
                            width: boundaryWidth,
                            height: boundaryHeight
                        },
                        enableExif: true
                    });

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#upload-demo').addClass('ready');
                        $uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function(){
                        });

                    }
                    reader.readAsDataURL($("input[name='file_photo']").prop("files")[0]);
                }
                else {
                    toastr.error('EX-901 画像の読み込みでエラーが発生しました。。');
                }
            }
        });
    }

    $(document).on('click', '.resize_upload', function (e) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function(response){
            $(".resize_image").iziModal('close');
            $(".speaker_list").iziModal('close');
            $("#fakeLoader").fakeLoader('start');
            ajax({
                url: '/image/speaker',
                type : "POST",
                dataType : "json",
                data : {'image' : response},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).then(
                function (data) {
                    if(data.errors){
                        $("#fakeLoader").fakeLoader('stop');
                        $(".speaker_list").iziModal('close');
                        toastr.error(data.errors);
                    }
                    changePattern(data.result.ai_id,data.result.img_url);
                },
                function (error) {
                    $(".speaker_list").iziModal('close');
                    errors(error.status);
                    return false;
                })
        })
    });

    function changePattern(ai_id, img_url)
    {
        var position = $("#position").val();
        if("1" === $("#init_flg").val()){
            $("#pecha_bk").attr('src', '/img/v2/talk/loading_small_white.gif?'+$.now());
            $("#pecha").attr('src', img_url);
            $($(".faceicon")[0]).find('img').attr('src', img_url);
            $('html').animate({scrollTop: 0}, 600);
            $('body').animate({scrollTop: 0}, 600);
            //$('.recreate_talk_box').css('top', "75%");
            $('.recreate_talk_box').show();
            $(".speaker_list").iziModal('close');
            getCompanion(ai_id, img_url);
            return;
        }
        ajax({
            url: '/image/change_speaker',
            type : "POST",
            dataType : "json",
            data : {'ai_id' : ai_id, 'img_url' : img_url, 'position' : position},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then(
            function (data) {
                if(data.errors){
                    $("#fakeLoader").fakeLoader('stop');
                    $(".speaker_list").iziModal('close');
                    toastr.error(data.errors);
                    return false;
                }                
                responses = data.talk_list.responses;
                var imgcount = 0;
                var readimgcount = 0;
                responses.forEach(response=>{
                    if(response.content_type=="image")
                        imgcount++;
                })
                if(imgcount==0)
                {
                    if(!generating)
                    {
                        localStorage.setItem("talkList", JSON.stringify(responses));
                        generateTalk(responses);
                        currentActiveindex = 0;
                    }
                    $("#pecha").attr('src', data.pecha);
                    $("#kucha").attr('src', data.kucha);
                    $("#fakeLoader").fakeLoader('stop');
                    $(".speaker_list").iziModal('close');
                }
                responses.forEach(response=>{
                    if(response.content_type=="image")
                    {
                        var img = new Image();
                        img.onload = function() {
                            readimgcount++;
                            if(imgcount===readimgcount)
                            {
                                if(!generating)
                                {
                                    localStorage.setItem("talkList", JSON.stringify(responses));
                                    generateTalk(responses);
                                    currentActiveindex = 0;
                                }
                                $("#pecha").attr('src', data.pecha);
                                $("#kucha").attr('src', data.kucha);
                                $("#fakeLoader").fakeLoader('stop');
                                $(".speaker_list").iziModal('close');
                            }
                        }
                        img.src = response.content;
                    }
            
                })
                $("#background").val(data.talk_list.status.background_url);
                document.getElementById("background").style.backgroundImage = `url('${ $("#background").val()}')`;
            },
            function (error) {
                $(".speaker_list").iziModal('close');
                errors(error.status);
                return false;
            })

        return true;
    }

    $(document).on('click', '#changemessage', function (event) {
       
        let position = responses[currentActiveindex].turn;
        $("#position").val(position);
        event.preventDefault();
        $(".talk_change").iziModal('open');
       
    });



    var candidate_flg = false;
    if($('.talk_change').length) {
        $(".talk_change").iziModal({
            height: "200px",
            width: "420px",
            top: 0,
            radius: "25px 25px 0 0",
            restoreDefaultContent: true,
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            onClosed: function () {
                $(".talk_change .iziModal-content").empty();
                $('body').removeAttr('style');
            },
            onOpening: function (modal) {
                candidate_flg = false;
               
                $("#fakeLoader").fakeLoader('start');
                if($(".btn_bottom_input_fixed").length){
                    return false;
                }
                $("#fakeLoader").fakeLoader('start');
                ajax({
                    url: '/talk/alternatives',
                    type: 'POST',
                    data: {
                        'position': $("#position").val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                }).then(
                    function (data) {
                        if (data.errors) {
                            $("#fakeLoader").fakeLoader('stop');
                            $(".talk_change").iziModal('close');
                            toastr.error(data.errors);
                            return false;
                        }
                        $(".talk_change .iziModal-content").append(data.result);
                        var deltaposition = 0;
                        var firstPosition = responses[0].position;
                        if(firstPosition != 0 && firstPosition != 1)
                        {
                            deltaposition = 2;
                        }
                        document.querySelector("#comment_change").innerHTML = document.querySelectorAll(position[parseInt((responses[currentActiveindex].position - deltaposition) / 2)])[currentActiveindex].innerHTML;
                        if(document.getElementById("comment_change"))
                        {
                            document.getElementById("comment_change").style.backgroundImage = `url('${$("#background").val()}')`;
                        }
                        
                        if(responses[currentActiveindex].content_type=="image")
                        {
                            $(".talk_change .iziModal-content").addClass("image")
                        }   
                        else{
                            $(".talk_change .iziModal-content").removeClass("image")
                        }                   
                        $('.main').height(window.innerHeight + 'px');
                        var $scrollAuto = $('.modal_comment_area');
                        $scrollAuto.animate({scrollTop: $scrollAuto[0].scrollHeight}, 600);
                        $(".talk_change > .iziModal-wrap").animate({scrollTop: 0}, 600);
                        $("#fakeLoader").fakeLoader('stop');
                        if(! data.status) {
                            alternatives_return(data.sub_process_id);
                            return true;
                        }
                        if("1" === $("#is_reload").val()){
                            $(".candidate").removeClass("candidate_ng");
                            $(".candidate").addClass("candidate_ok");
                            candidate_flg = true;
                        }
                        return true;
                    },
                    function (error) {
                        errors(error.status);
                        return false;
                })
            }
        });
    }

    var alter_flg = false;
    var change_flg = false;
    var fixed_flg = false;

    function alternatives_return(id)
    {
        if(change_flg){
            return true;
        }
        if(fixed_flg){
            setTimeout(alternatives_return, 2000, id);
            return true;
        }
        alter_flg = true;
        ajax({
            url: '/talk/alternatives_return',
            type: 'POST',
            data: {
                'sub_process_id': id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
        }).then(
            function (data) {
                alter_flg = false;
                if (data.errors) {
                    toastr.error(data.errors);
                    return false;
                }
                let loop = true;
                let replaces = $(".replace");
                let records  = data.result.responses;
                $.each(records, function(idx, val) {
                    if(0 === $(replaces[idx]).length){
                        loop = false;
                        return false;
                    }
                    if(1 === $(replaces[idx]).data('click')){
                        return true;
                    }
                    $(replaces[idx]).data('click', 1);
                    $(replaces[idx]).data('remark_id', val.remark_id);
                    if('text' === val.content_type){
                        $(replaces[idx]).find('p').text(val.content);
                    }
                });
                if(! data.status && loop){
                    setTimeout(alternatives_return, 2000, id);
                    return true;
                }
                if("1" === $("#is_reload").val()){
                    $(".candidate").removeClass("candidate_ng");
                    $(".candidate").addClass("candidate_ok");
                    candidate_flg = true;
                }
                return true;
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }

    $(document).on('click', '#candidate', function (event) {
        candidate();
    });

    function candidate()
    {
        if(! candidate_flg){
            return false;
        }
        if(fixed_flg){
            setTimeout(alternatives_return, 2000);
            return true;
        }
        candidate_flg = false;
        $(".candidate").removeClass("candidate_ok");
        $(".candidate").addClass("candidate_ng");
        $('.main').height(window.innerHeight + 'px');
        var $scrollAuto = $('.modal_comment_area');
        $scrollAuto.animate({scrollTop: $scrollAuto[0].scrollHeight}, 600);
        $(".talk_change > .iziModal-wrap").animate({scrollTop: 0}, 600);
        $("#fakeLoader").fakeLoader('start');
        ajax({
            url: '/talk/candidate',
            type: 'POST',
            data: {
                'position': $("#position").val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
        }).then(
            function (data) {
                if (data.errors) {
                    $("#fakeLoader").fakeLoader('stop');
                    $(".talk_change").iziModal('close');
                    toastr.error(data.errors);
                    return false;
                }
                $(".change_list").empty();
                $(".change_list").append(data.result);
                $("#is_reload").val(data.is_reload);
                $("#fakeLoader").fakeLoader('stop');
                if(! data.status) {
                    alternatives_return(data.sub_process_id);
                    return true;
                }
                if("1" === $("#is_reload").val()){
                    $(".candidate").removeClass("candidate_ng");
                    $(".candidate").addClass("candidate_ok");
                    candidate_flg = true;
                }
                return true;
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }

    // image
    if($('.talk_image_list').length) {
        $(".talk_image_list").iziModal({
            top: 220,
            width: 420,
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            onClosed: function () {
                $(".talk_image_list .iziModal-content").empty();
            },
            onOpening: function (modal) {
                ajax({
                    url: '/image/list',
                    type: 'get',
                    dataType: 'json',
                }).then(
                    function (data) {
                        if (data.errors) {
                            $("#fakeLoader").fakeLoader('stop');
                            $(".talk_image_list").iziModal('close');
                            toastr.error(data.errors);
                            return false;
                        }
                        $(".talk_image_list .iziModal-content").append(data.result);
                        $("#fakeLoader").fakeLoader('stop');
                    },
                    function (error) {
                        $(".talk_image_list").iziModal('close');
                        errors(error.status);
                        return false;
                    })
            }
        });
    }

    $(document).on('click', '#talk_image_list', function (event) {
        if($('#switch1').prop("checked")){
            return false;
        }
        event.preventDefault();
        $("#fakeLoader").fakeLoader('start');
        $(".talk_image_list").iziModal('open');
    });

    $(document).on('click', '#talk_images li', function (e) {
        const img_index = $(this).index();
        if (0 !== img_index) {
            $("#fakeLoader").fakeLoader('start');
            let param = {
                'text': '',
                'img_id': $(this).children().attr("id"),
                'img_url': $(this).children().attr("src"),
                'user_content': 0,
                'remark_id': ''
            }
            $(".talk_image_list").iziModal('close');
            $(".talk_change").iziModal('close');
            changeTalk(param);
        }
        return true;
    });

    $(document).on('change','input[name="image_file_photo"]',function(){
        var fd = new FormData();
        if ($("input[name='image_file_photo']").val()!== '') {
            fd.append( "file", $("input[name='image_file_photo']").prop("files")[0] );
        }
        $(".talk_image_list").iziModal('close');
        $(".talk_change").iziModal('close');
        $("#fakeLoader").fakeLoader('start');
        ajax({
            url: '/image/talk',
            type : "POST",
            dataType : "json",
            data : fd,
            processData : false,
            contentType : false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then(
            function (data) {
                if (data.errors) {
                    $(".talk_image_list .iziModal-content").empty();
                    $("#fakeLoader").fakeLoader('stop');
                    toastr.error(data.errors);
                    return false;
                }
                let param = {
                    'text': '',
                    'img_id': data.result.img_id,
                    'img_url': data.result.img_url,
                    'user_content': 1,
                    'remark_id': ''
                }
                changeTalk(param);
                return true;
            },
            function (error) {
                errors(error.status);
                return false;
            })
    });

    $(document).on('click', '#word_set', function (event) {
        if($('#switch1').prop("checked") || $(this).children('input').hasClass('talk_box')){
            return false;
        }
        var html =
            '<input type="text" name="word_set" maxlength="64" class="talk_box" id="talk_box">' +
            '<input type="submit" id="word_send" class="talk_send" value="送信">';
        $(this).html(html);
    });

    $(document).on('click', '.replace p', function (event) {
        if(! $(this).parent().data('click') || $('#switch1').prop("checked")){
            return false;
        }
        let param = {
            'text': $(this).text(),
            'img_id': '',
            'img_url': '',
            'user_content': 0,
            'remark_id': $(this).parent().data('remark-id')
        }
        $("#fakeLoader").fakeLoader('start');
        $(".talk_change").iziModal('close');
        changeTalk(param);
    });

    $(document).on('click', '#word_send', function (e) {
        word_send();
        return true;
    });

    function word_send(){
        let send_text = $(".talk_box").val().replace(/^[\s|　]+|[\s|　]+$/g,'');
        if(send_text.length == 0){
            return false;
        }
        let param = {
            'text': send_text,
            'img_id': '',
            'img_url': '',
            'user_content': 1,
            'remark_id': ''
        }
        $("#fakeLoader").fakeLoader('start');
        $(".talk_change").iziModal('close');
        changeTalk(param);
    }

    // talk
    function changeTalk(data)
    {
        var change_obj = $('[data-position="' + $("#position").val() + '"]').find('p');
        change_obj.removeClass('says');
        change_obj.addClass('think');
        replaceTalk(data);
        return true;
    }

    function replaceTalk(data)
    {
        change_flg = true;
        if(alter_flg){
            setTimeout(replaceTalk, 1000, data);
            return false;
        }

        ajax({
            url: '/talk/change',
            type: 'POST',
            data: {
                'position': $("#position").val(),
                'text': data.text,
                'img_id': data.img_id,
                'img_url': data.img_url,
                'user_content': data.user_content,
                'remark_id': data.remark_id,
                'turn':data.turn,
                'fixed_positions': $("#fixed_positions").val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
        }).then(
            function (data) {
                change_flg = false;
                if (data.errors) {
                    $("#fakeLoader").fakeLoader('stop');
                    toastr.error(data.errors);
                    return false;
                }

               responses = data.talk_list.responses;
               var imgcount = 0;
               var readimgcount = 0;
               responses.forEach(response=>{
                   if(response.content_type=="image")
                       imgcount++;
               })

               if(imgcount==0)
               {
                    if(!generating)
                    {
                    localStorage.setItem("talkList", JSON.stringify(responses)); 
                    generateTalk(responses);
                    currentActiveindex = 0;
                    }

                    $("#fixed_positions").val(data.fixed_positions);
                    $("#fakeLoader").fakeLoader('stop');
               }

               responses.forEach(response=>{
                   if(response.content_type=="image")
                   {
                       var img = new Image();
                       img.onload = function() {
                           readimgcount++;
                           if(imgcount===readimgcount){
                                if(!generating)
                                {
                                localStorage.setItem("talkList", JSON.stringify(responses)); 
                                generateTalk(responses);
                                currentActiveindex = 0;
                                }
                
                                $("#fixed_positions").val(data.fixed_positions);
                                $("#fakeLoader").fakeLoader('stop');
                            }
                        }
                       img.src = response.content;
                   }           
               })
               $("#background").val(data.talk_list.status.background_url)
               document.getElementById("background").style.backgroundImage = `url('${ $("#background").val()}')`;              
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }

    // movie
    $(document).on('click', '#create_movie', function (event) {
        $('html').animate({scrollTop: 0}, 50);
        $('body').animate({scrollTop: 0}, 50);
        $('body').css('overflow', 'hidden');
        $("#fakeLoader").fakeLoader('start');
        var winW = $("body").width();
        var winH = $("body").height();
        if (navigator.userAgent.match(/iPhone|Android.+Mobile/)) {
            winW = $(window).width();
            winH = $(window).height();
        }
        var spinnerW = $('.create_movie').outerWidth();
        var spinnerH = $('.create_movie').outerHeight();

        $('.create_movie').css({
            'left':(winW/2)-(spinnerW/2),
            'top':(winH/2)-(spinnerH/2)
        });
        $("#loading_movie").attr('src', '/img/v2/talk/loading.gif?'+$.now());
        $(".create_movie").show();
        ajax({
            url: '/movie',
            type: 'POST',
            data: {},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
        }).then(
            function (data) {
                change_flg = false;
                if (data.errors) {
                    $("#fakeLoader").fakeLoader('stop');
                    toastr.error(data.errors);
                    return false;
                }
                window.location.href = "/movie";
            },
            function (error) {
                errors(error.status);
                return false;
            })
    });

    $(document).on('click', '.template_list li', function (event) {
        // $("#fakeLoader").fakeLoader('start');
        $("#template_id").val($(this).data('template-id'));
        $(".genre_name").text($(this).text());
        $("#template_name").text($(this).text());
        $(".template_list").iziModal('close');
        
        // getCompanion(0, null, 'recreate');
    });

    // companion
    function getCompanion(id, img_url, mode='new')
    {
        ajax({
            url: '/image/companion',
            type: 'POST',
            data: {
                'id':id,
                'img_url': img_url,
                'template_id' : $("#template_id").val(),
                'mode' : mode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
        }).then(
            function (data) {
                if (data.errors) {
                    $("#fakeLoader").fakeLoader('stop');
                    $(".speaker_list .iziModal-content").empty();
                    toastr.error(data.errors);
                    return false;
                }
                if('recreate'== data.result.mode){
                    $("#pecha_bk").attr('src', '/img/v2/talk/loading_small_white.gif?'+$.now());
                    $("#pecha").attr('src', data.result.talker_list[0].img_url);
                }
                $("#kucha_bk").attr('src', '/img/v2/talk/loading_small_white.gif?'+$.now());
                $("#kucha").attr('src', data.result.talker_list[1].img_url);
                $("#template_id").val(data.result.template.template_id);
                $(".genre_name").text(data.result.template.name);
                create_talk(data.result);
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }

    let ajax = function(arg) {
        let opt = $.extend({}, $.ajaxSettings, arg);
        return $.ajax(opt);
    };

    $(document).on('click', '#back_button_header_top', function (event) {
        message_dialog('#message_conf_top', 'body');
    });

    $(document).on('click', '#update', function (e) {
        
        var responses = JSON.parse(localStorage.getItem("talkList"));
        generateTalk(responses);
        currentActiveindex = 0;
    });

    $(document).on('click', '.finish-modal', function (event) {
        event.preventDefault();
        document.querySelector('#template-section').style.visibility = "unset"
        document.querySelector('.finish-modal').style.display = 'none';
        document.getElementById("statusimg").src="/img/v2/talk/making_talk2.gif";
        document.querySelectorAll(".section02").forEach(element => {
           element.classList.add("disable");
        });
        document.querySelectorAll(".section02-talk-part").forEach(element=>{
            element.classList.add("disable");
        });
        document.querySelector('.section03').style.display = 'flex';
        document.querySelector('.section03').classList.remove("disable");
        document.querySelector('.section04').classList.remove("disable");
        generating = false;
        generateSliders();
        // setActive();
    });

    $(document).off('click', '.switchArea').on('click', '.switchArea', function() {
        if($("#switch1").prop("checked")){
            $("#switch1").prop("checked", false);
        }
        else{
            $("#switch1").prop("checked", true);
        }
        fixed_talk($("#switch1").prop("checked"));
    });

    function fixed_talk(checked){
        var fixed  = $("#fixed_positions").val();
        fixed = fixed.split(',');
        var position = $("#position").val();
        var index = fixed.indexOf(position);
        if(checked){
            fixed_change(1);
            $(".talk_box").prop('disabled', true);
            if (index === -1){
                fixed.push(position);
                $(".change_talk_box ul").addClass('fixed');
                var talk_obj = $($(".talk_contents_area")[position]).find('p')
                var change_obj = $($(".change_contents_area")[position]).find('p')
                if(0 < change_obj.length) {
                    talk_obj.removeClass('says');
                    talk_obj.addClass('locked');
                    change_obj.removeClass('says');
                    change_obj.addClass('locked');
                }
                else{
                    var talk_obj = $($(".talk_contents_area")[position]).find('div')
                    var change_obj = $($(".change_contents_area")[position]).find('div')
                    talk_obj.removeClass('file_img');
                    talk_obj.addClass('file_img_locked');
                    change_obj.removeClass('file_img');
                    change_obj.addClass('file_img_locked');
                }
            }
        }
        else{
            $(".talk_box").prop('disabled', false);
            fixed_change(0);
            if (index !== -1) {
                fixed.splice(index, 1);
                $(".change_talk_box ul").removeClass('fixed');
                var talk_obj = $($(".talk_contents_area")[position]).find('p')
                var change_obj = $($(".change_contents_area")[position]).find('p')
                if(0 < change_obj.length) {
                    talk_obj.removeClass('locked');
                    talk_obj.addClass('says');
                    change_obj.removeClass('locked');
                    change_obj.addClass('says');
                }
                else{
                    var talk_obj = $($(".talk_contents_area")[position]).find('div')
                    var change_obj = $($(".change_contents_area")[position]).find('div')
                    talk_obj.removeClass('file_img_locked');
                    talk_obj.addClass('file_img');
                    change_obj.removeClass('file_img_locked');
                    change_obj.addClass('file_img');
                }
            }
        }
        var fixed = fixed.join(',');
        if(fixed.match(/^,/)){
            fixed = fixed.slice(1);
        }
        $("#fixed_positions").val(fixed);
    }

    function fixed_change(fixed){
        if(alter_flg){
            setTimeout(fixed_change, 1000, fixed);
            return false;
        }
        $("#fakeLoader").fakeLoader('start');
        fixed_flg = true;
        $.ajax({
            url: '/talk/fixed',
            type : "POST",
            dataType : "json",
            data : {'position' : $("#position").val(), 'fixed' : fixed},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then(
            function (data) {
                fixed_flg = false;
                if (data.errors) {
                    $(".talk_image_list .iziModal-content").empty();
                    $("#fakeLoader").fakeLoader('stop');
                    toastr.error(data.errors);
                    return false;
                }
                $("#fakeLoader").fakeLoader('stop');
                return true;
            },
            function (error) {
                errors(error.status);
                return false;
            })
    }

    $(document).on('keydown', "#talk_box", function (event) {
        if (event.key === "Enter") {
            word_send();
        }
    });
});

function create_talk(talker_list=null)
{
    $('html').animate({scrollTop: 0}, 600);
    $('body').animate({scrollTop: 0}, 600);
    //$('.recreate_talk_box').css('top', "75%");
    $('.recreate_talk_box').show();
    talker_list.template_id = $('#template_id').val();
    talker_list.init_flg = $('#init_flg').val();
    $.ajax({
        url: '/talk',
        type : "POST",
        data : talker_list,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    }).then(
        function (data) {
            if(data.errors){
                $("#fakeLoader").fakeLoader('stop');
                $('.recreate_talk_box').hide();
                toastr.error(data.errors);
                return false;
            }
            $('.recreate_talk_box').hide()
            responses = data.talk_list.responses;
            var imgcount = 0;
            var readimgcount = 0;
            responses.forEach(response=>{
                if(response.content_type=="image")
                    imgcount++;
            })
            if(imgcount==0)
            {
                generateTalk(responses)
                currentActiveindex = 0;
                $("#fakeLoader").fakeLoader('stop');
            }
            responses.forEach(response=>{
                if(response.content_type=="image")
                {
                    var img = new Image();
                    img.onload = function() {
                        readimgcount++;
                        if(imgcount==readimgcount)
                        {
                            generateTalk(responses)
                            currentActiveindex = 0;
                            $("#fakeLoader").fakeLoader('stop');
                        }
                    }
                    img.src = response.content;
                }
        
            })
            $("#background").val(data.talk_list.status.background_url)
            document.getElementById("background").style.backgroundImage = `url('${$("#background").val()}')`;
            var init_flg = $("#init_flg").val();
            $("#fixed_positions").val('');
            if(0 < data.fixed_positions.length){
                $("#fixed_positions").val(data.fixed_positions);
            }
            $("#init_flg").val(0);
            if("1" === init_flg){
                var offset = $($(".faceicon")[1]).offset();
                var width = $($(".faceicon")[1]).width() / 2;
                var left = undefined;
                var right = 4;
                var top = offset.top-70;
                var tips = new Tips();
                tips.show('tips_right',"タップしてKuChaも<br>変更できます", 1500, top, left, right);
                setTimeout(function(){
                    var offset = $($('.talk_contents_area')[0]).offset();
                    var width = $($('.talk_contents_area')[0]).width() / 2;
                    var left = 81+width-96;
                    var top = offset.top-70;
                    var tips = new Tips();
                    tips.show('tips',"タップして<br>トークを編集できます", 1500, top, left);
                },1600);
            }
        },
        function (error) {
            errors(error.status);
            return false;
        })
}

function errors(code){
    $("#fakeLoader").fakeLoader('stop');
    window.location.href = '/error/'+code;
}

function message_dialog(id, append) {
    var message = '#message_conf_top' === id ? 'みんなの投稿画面に戻ります。' : 'トーク作成画面に戻ります。';
    message += '\nよろしいですか？';
    var locate = '#message_conf_top' === id ? '/' : '/talk';
    if (!confirm(message)) {
        return false;
    } else {
        window.location.href = locate;
    }
}




function sleep(ms) {
    return new Promise(resolve => (setTimeout(resolve, ms)));
}

function renderedit(item,index,activeindex) {
    document.getElementById("statusimg").src="/img/v2/talk/making_talk2.gif";
    var firstPosition = responses[0].position;
    var deltaposition = 0;
    if(firstPosition != 0 && firstPosition != 1)
    {
        deltaposition = 2;
    }
    var element = document.querySelector(position[parseInt((item.position-deltaposition)/2)]);
    
        if(item.position % 2 === 0)
        {
            element.setAttribute("data-userid", item.ai_id);
            element.innerHTML = `<div class="section02-part02 ${mb[parseInt((item.position-deltaposition)/2)]}">
                                    <div class="section02-part02-img">
                                        <img src=${item.ai_img_url} alt="">
                                    </div>
                                </div>`
           
            var talkpartElement = document.querySelector(`${position[parseInt((item.position-deltaposition)/2)]} .section02-part02`);
            if(item.content_type=="text")
            {
                if(item.voice_url!=="")
                {
                    talkpartElement.innerHTML += `<div class="section02-part02-text b-15">
                                                ${item.content}
                                                <div class="with-voice" onclick="playaudio(this)" data-voiceURL=${item.voice_url}></div>
                                            </div>`
                }
                else
                {
                    talkpartElement.innerHTML += `<div class="section02-part02-text b-15">
                                                    ${item.content}
                                                </div>`
                }
                
            }
            if(item.content_type=="image")
            {
                if(activeindex===index)
                {
                    talkpartElement.innerHTML += `<div class="section02-part02-img01 b-15">
                        <img src=${item.content} alt="">
                    </div>`
                }
                else{
                    talkpartElement.innerHTML += ""
                }

            }
        }
        else{
            element.setAttribute("data-userid", item.ai_id);
            element.innerHTML = `<div class="section02-part02  ${mb[parseInt((item.position-deltaposition)/2)]}">
                                    <div></div>                        
                                    <div style="order: 1" class="section02-part02-img">
                                        <img src=${item.ai_img_url} alt="">
                                    </div>
                                </div>`
        
            var talkpartElement = document.querySelector(`${position[parseInt((item.position-deltaposition)/2)]} .section02-part02`);
            if(item.content_type=="text")
            {
                if(item.voice_url!=="")
                {
                    talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                        <img src=${item.ai_img_url} alt="">
                    </div>
                    <div  class="section02-part02-text01 b-15">
                        ${item.content}
                        <div class="with-voice" onclick="playaudio(this)" data-voiceURL=${item.voice_url}></div>
                    </div>`
                }
                else{
                    talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                        <img src=${item.ai_img_url} alt="">
                    </div>
                    <div class="section02-part02-text01 b-15">
                        ${item.content}
                    </div>`
                }
            }
            if(item.content_type=="image")
            {
                if(activeindex===index)
                {
                    talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                                                        <img src=${item.ai_img_url} alt="">
                                                    </div>
                                                <div class="section02-part02-img02 b-15">
                                                    <img src=${item.content} alt="">
                                                </div>`
                }
                else{
                    talkpartElement.innerHTML += "";
                }
                
            }
        }
    
}

function generateSliders(){
    var slider = [];
    var firstPosition = responses[0].position;
    var deltaposition = 0;
    if(firstPosition != 0 && firstPosition != 1)
    {
        deltaposition = 2;
    }
    for(var iterator = 0; iterator< responses.length; iterator++)
    {
        position.forEach(p=>{
            document.querySelector(p).innerHTML="";
            document.querySelector(p).removeAttribute("data-userid");
            document.querySelector(p).classList.remove("activemessage");
        });
        for(var i = 0; i<=iterator; i++)
        {
            renderedit(responses[i],i,iterator);
            if(i==iterator)
            {
                document.querySelector(position[parseInt((responses[i].position-deltaposition)/2)]).classList.add("activemessage");
                          
            }
        }
        slider.push(document.querySelector("#message-area").innerHTML)
    }
    var wrapper = slider.map(function mapfunc(divitem){return `<div class="swiper-slide">${divitem}</div>`})
    var wrapperhtml="";
    wrapper.forEach(function iterate(item){wrapperhtml+=item});
    var sliders = `<div class="swiper-container" id="slider">
                        <div class="swiper-wrapper">
                           ${wrapperhtml}
                        </div>
                        
                    </div>
                  `
    document.querySelector("#message-area").innerHTML = sliders;
    var swiper = new Swiper(".swiper-container", {
        slidesPerView: 1,
        spaceBetween: 0,
        centeredSlides: true,
        grabCursor: true,
        loop: false,
        pagination: {
            el: ".swiper-pagination",
            clickable: true
        },
        autoplay: false,
        navigation: {
            nextEl: ".nextmessage",
            prevEl: ".prevemessage"
        }
    }); 
    setActive();
    swiper.on('transitionEnd', function() {
        currentActiveindex =  swiper.realIndex;
        setActive();
    });

}

function setActive(){
    if(currentActiveindex==0)
    {
        document.querySelectorAll(".prevemessage").forEach(element=>{
            element.style.visibility = "hidden";
        });
    }
    else
    {
        document.querySelectorAll(".prevemessage").forEach(element=>{
            element.style.visibility = 'unset';
        });
    }
    if(currentActiveindex==responses.length-1)
    {
        document.querySelectorAll(".nextmessage").forEach(element=>{
            element.style.visibility = "hidden"
        });
    }
    else{
        document.querySelectorAll(".nextmessage").forEach(element=>{
            element.style.visibility = 'unset';
        });
    }

    var firstPosition = responses[0].position;
    var deltaposition = 0;
    if(firstPosition != 0 && firstPosition != 1)
    {
        deltaposition = 2;
    }
    if((responses[currentActiveindex].position == firstPosition) || (responses[currentActiveindex].position == 6 && (firstPosition == 0 || firstPosition == 1)) || (responses[currentActiveindex].position == 7 && (firstPosition == 0 || firstPosition == 1))){
        var messagearea = document.getElementsByClassName("seciton02-message-area")[0];
        messagearea.scrollTo({
            top: ((responses[currentActiveindex].position - deltaposition)/2) * 100,
            behavior: 'smooth'
        })
    }

}

async function render(item) {
    var element = document.querySelector(position[parseInt(item.position/2)]);
    var userid = element.getAttribute("data-userid");
    if (userid===null || userid!=item.ai_id)
    {
        if(item.position % 2 === 0)
        {
            element.setAttribute("data-userid", item.ai_id);
            await sleep(timeCharactertoAvatar);
            element.innerHTML = `<div class="section02-part02 ${mb[parseInt(item.position/2)]}">
                                    <div class="section02-part02-img">
                                        <img src=${item.ai_img_url} alt="">
                                    </div>
                                </div>`
            await sleep(timeAvatarToballoon);
            var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
            if(item.content_type=="text")
            {
               
                talkpartElement.innerHTML += `<div class="section02-part02-text b-15">
                </div>`
                var talkparttextelement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text`);
                if(item.voice_url!=="")
                {
                    var voicediv = document.createElement("div");
                    voicediv.className="with-voice";
                    voicediv.dataset.voiceurl = item.voice_url;
                    voicediv.setAttribute("onclick","playaudio(this)");
                    talkparttextelement.appendChild(voicediv)
                }
                var div = document.createElement('div');
                div.innerHTML = item.content;
                var childNodes = div.childNodes;
                var i = 0
                while(i < childNodes.length){
                    if(childNodes[i].nodeName=="#text")
                    {
                            
                        var span = document.createElement("span");
                        span.className = `span-${i}`
                        talkparttextelement.appendChild(span);
                        var typeindex = 0;
                        talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text .span-${i}`);
                        while(typeindex<childNodes[i].data.length){
                            talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                            typeindex++;
                            await sleep(75); 
                        }
                    }
                    if(childNodes[i].nodeName=="A")
                    {
                        var atagtext = childNodes[i].innerHTML;
                        var atag = childNodes[i].cloneNode(true);
                        atag.innerHTML="";
                        talkparttextelement.appendChild(atag);
                        talkpartTextLink = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text a`);
                        var typeindex = 0;
                        while(typeindex<atagtext.length){
                            talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                            typeindex++;
                            await sleep(75); 
                        }
                    }
                    i++;
                }
            }
            if(item.content_type=="image")
            {
                talkpartElement.innerHTML += `<div class="section02-part02-img01 b-15">
                                                <img src=${item.content} alt="">
                                                </div>`
                await sleep(timeeraseImage);
                talkpartElement.innerHTML = `<div class="section02-part02-img">
                                                <img src=${item.ai_img_url} alt="">
                                            </div>` 
            }
        }
        else{
            element.setAttribute("data-userid", item.ai_id);
            await sleep(timeCharactertoAvatar);
            element.innerHTML = `<div class="section02-part02  ${mb[parseInt(item.position/2)]}">
                                    <div></div>                        
                                    <div style="order: 1" class="section02-part02-img">
                                        <img src=${item.ai_img_url} alt="">
                                    </div>
                                </div>`
            await sleep(timeAvatarToballoon);
            var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
            if(item.content_type=="text")
            {
                    talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                        <img src=${item.ai_img_url} alt="">
                    </div>
                    <div class="section02-part02-text01 b-15">
                    </div>`
                
                var talkparttextelement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01`);

                if(item.voice_url!=="")
                {
                    var voicediv = document.createElement("div");
                    voicediv.className="with-voice";
                    voicediv.dataset.voiceurl = item.voice_url;
                    voicediv.setAttribute("onclick","playaudio(this)");
                    talkparttextelement.appendChild(voicediv)
                }
                var div = document.createElement('div');
                div.innerHTML = item.content;
                var childNodes = div.childNodes;
                var i = 0
                while(i < childNodes.length){
                    if(childNodes[i].nodeName=="#text")
                    {
                            
                        var span = document.createElement("span");
                        span.className = `span-${i}`
                        talkparttextelement.appendChild(span);
                        var typeindex = 0;
                        talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 .span-${i}`);
                        while(typeindex<childNodes[i].data.length){
                            talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                            typeindex++;
                            await sleep(75); 
                        }
                    }
                    if(childNodes[i].nodeName=="A")
                    {
                        var atagtext = childNodes[i].innerHTML;
                        var atag = childNodes[i].cloneNode(true);
                        atag.innerHTML="";
                        talkparttextelement.appendChild(atag);
                        talkpartTextLink = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 a`);
                        var typeindex = 0;
                        while(typeindex<atagtext.length){
                            talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                            typeindex++;
                            await sleep(75); 
                        }
                    }
                    i++;
                }
            }
            if(item.content_type=="image")
            {
                talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                                                <img src=${item.ai_img_url} alt="">
                                            </div>
                                            <div class="section02-part02-img02 b-15">
                                                    <img src=${item.content} alt="">
                                            </div>`
                await sleep(timeeraseImage);
                talkpartElement.innerHTML = `<div></div>
                                                <div style="order: 1" class="section02-part02-img">
                                                    <img src=${item.ai_img_url} alt="">
                                                </div>`
                                                    
            }
        }
    }
    else{

        if(item.position % 2 === 0){
            await sleep(timeCharactertoAvatar);
            var preveimg = true;
            var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text`);
            if(talkpartElement){
                talkpartElement.innerHTML = "";
                preveimg = false;
            }
            await sleep(timeAvatarToballoon);
            if(preveimg){                    
                if(item.content_type=="text")
                {
                    var element =  document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
                    element.innerHTML += `<div class="section02-part02-text b-15">
                    </div>`
                                     
                    var typeindex = 0;
                    talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text`);
                    
                    if(item.voice_url!=="")
                    {
                        var voicediv = document.createElement("div");
                        voicediv.className="with-voice";
                        voicediv.dataset.voiceurl = item.voice_url;
                        voicediv.setAttribute("onclick","playaudio(this)");
                        talkpartElement.appendChild(voicediv)
                    }
                    var div = document.createElement('div');
                    div.innerHTML = item.content;
                    var childNodes = div.childNodes;
                    var i = 0
                    while(i < childNodes.length){
                        if(childNodes[i].nodeName=="#text")
                        {
                                
                            var span = document.createElement("span");
                            span.className = `span-${i}`
                            talkpartElement.appendChild(span);
                            var typeindex = 0;
                            talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text .span-${i}`);
                            while(typeindex<childNodes[i].data.length){
                                talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        if(childNodes[i].nodeName=="A")
                        {
                            var atagtext = childNodes[i].innerHTML;
                            var atag = childNodes[i].cloneNode(true);
                            atag.innerHTML="";
                            talkpartElement.appendChild(atag);
                            talkpartTextLink = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text a`);
                            var typeindex = 0;
                            while(typeindex<atagtext.length){
                                talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        i++;
                    }                
                }
                if(item.content_type=="image")
                {
                    var element =  document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
                    element.innerHTML += `<div class="section02-part02-img01 b-15">
                                                    <img src=${item.content} alt="">
                                                </div>`
                    await sleep(timeeraseImage);
                    element.innerHTML = `<div class="section02-part02-img">
                                                    <img src=${item.ai_img_url} alt="">
                                                </div>`
                }
            }
            else{
                if(item.content_type=="text")
                {
                    
                    if(item.voice_url!=="")
                    {
                        var voicediv = document.createElement("div");
                        voicediv.className="with-voice";
                        voicediv.dataset.voiceurl = item.voice_url;
                        voicediv.setAttribute("onclick","playaudio(this)");
                        talkpartElement.appendChild(voicediv)
                    }
                    var div = document.createElement('div');
                    div.innerHTML = item.content;
                    var childNodes = div.childNodes;
                    var i = 0
                    while(i < childNodes.length){
                        if(childNodes[i].nodeName=="#text")
                        {
                                
                            var span = document.createElement("span");
                            span.className = `span-${i}`
                            talkpartElement.appendChild(span);
                            var typeindex = 0;
                            talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text .span-${i}`);
                            while(typeindex<childNodes[i].data.length){
                                talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        if(childNodes[i].nodeName=="A")
                        {
                            var atagtext = childNodes[i].innerHTML;
                            var atag = childNodes[i].cloneNode(true);
                            atag.innerHTML="";
                            talkpartElement.appendChild(atag);
                            talkpartTextLink = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text a`);
                            var typeindex = 0;
                            while(typeindex<atagtext.length){
                                talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        i++;
                    } 
                }
                else
                {
                    
                    
                    var element =  document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
                    element.innerHTML = `<div class="section02-part02-img">
                                            <img src=${item.ai_img_url} alt="">
                                            </div>
                                            <div class="section02-part02-img01 b-15">
                                                        <img src=${item.content} alt="">
                                                    </div>`
                        await sleep(timeeraseImage);
                        element.innerHTML = `<div class="section02-part02-img">
                                                <img src=${item.ai_img_url} alt="">
                                            </div>`
            
                    }
            }
        }
        else{
            await sleep(timeCharactertoAvatar);
            var preveimg = true;
            var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01`);
           
            if(talkpartElement)
            {
                talkpartElement.innerHTML = ""
                preveimg = false;
            }
            
            await sleep(timeAvatarToballoon);
            if(preveimg){
                var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);

                if(item.content_type=="text")
                {
                    talkpartElement.innerHTML += `<div class="section02-part02-text01 b-15">
                                                </div>`
                    var talkparttextelement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01`);
                    if(item.voice_url!=="")
                    {
                        var voicediv = document.createElement("div");
                        voicediv.className="with-voice";
                        voicediv.dataset.voiceurl = item.voice_url;
                        voicediv.setAttribute("onclick","playaudio(this)");
                        talkparttextelement.appendChild(voicediv)
                    }
                    var div = document.createElement('div');
                    div.innerHTML = item.content;
                    var childNodes = div.childNodes;
                    var i = 0
                    while(i < childNodes.length){
                        if(childNodes[i].nodeName=="#text")
                        {
                                
                            var span = document.createElement("span");
                            span.className = `span-${i}`
                            talkparttextelement.appendChild(span);
                            var typeindex = 0;
                            talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 .span-${i}`);
                            while(typeindex<childNodes[i].data.length){
                                talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        if(childNodes[i].nodeName=="A")
                        {
                            var atagtext = childNodes[i].innerHTML;
                            var atag = childNodes[i].cloneNode(true);
                            atag.innerHTML="";
                            talkpartElement.appendChild(atag);
                            talkparttextelement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 a`);
                            var typeindex = 0;
                            while(typeindex<atagtext.length){
                                talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        i++;
                    } 
                }
                if(item.content_type=="image")
                {
                    talkpartElement.innerHTML += `<div class="section02-part02-img02 b-15">
                                                        <img src=${item.content} alt="">
                                                </div>`
                    await sleep(timeeraseImage);
                    talkpartElement.innerHTML = `<div></div>
                                                    <div style="order: 1" class="section02-part02-img">
                                                        <img src=${item.ai_img_url} alt="">
                                                    </div>`
                }
            }
            else{
                if(item.content_type=="text")
                {
                   
                    if(item.voice_url!=="")
                    {
                        var voicediv = document.createElement("div");
                        voicediv.className="with-voice";
                        voicediv.dataset.voiceurl = item.voice_url;
                        voicediv.setAttribute("onclick","playaudio(this)");
                        talkpartElement.appendChild(voicediv)
                    }
                    var div = document.createElement('div');
                    div.innerHTML = item.content;
                    var childNodes = div.childNodes;
                    var i = 0
                    while(i < childNodes.length){
                        if(childNodes[i].nodeName=="#text")
                        {
                                
                            var span = document.createElement("span");
                            span.className = `span-${i}`
                            talkpartElement.appendChild(span);
                            var typeindex = 0;
                            talkpartTextspan = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 .span-${i}`);
                            while(typeindex<childNodes[i].data.length){
                                talkpartTextspan.innerHTML = childNodes[i].data.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        if(childNodes[i].nodeName=="A")
                        {
                            var atagtext = childNodes[i].innerHTML;
                            var atag = childNodes[i].cloneNode(true);
                            atag.innerHTML="";
                            talkpartElement.appendChild(atag);
                            talkpartTextLink = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02 .section02-part02-text01 a`);
                            var typeindex = 0;
                            while(typeindex<atagtext.length){
                                talkpartTextLink.innerHTML = atagtext.substring(0, typeindex+1)
                                typeindex++;
                                await sleep(75); 
                            }
                        }
                        i++;
                    } 
                }
                if(item.content_type=="image")
                {
                    var talkpartElement = document.querySelector(`${position[parseInt(item.position/2)]} .section02-part02`);
                    talkpartElement.innerHTML = `<div style="order: 1" class="section02-part02-img">
                                                    <img src=${item.ai_img_url} alt="">
                                                </div>
                                                <div class="section02-part02-img02 b-15">
                                                        <img src=${item.content} alt="">
                                                </div>`
                    await sleep(timeeraseImage);
                    talkpartElement.innerHTML = `<div></div>
                                            <div style="order: 1" class="section02-part02-img">
                                                <img src=${item.ai_img_url} alt="">
                                            </div>`
                }
            }
            
        }
    }
    resolve => setTimeout(resolve, 0)
}

async function generateTalk(res){
    var slider = document.getElementById('slider');
    if(slider)
    {
        slider.remove();
        var messagearea = document.getElementById('message-area');
        messagearea.innerHTML=`<div class="position01 section02-talk-part">
                                </div>
                                <div class="position23 section02-talk-part">
                                </div>
                                <div class="position45 section02-talk-part">
                                </div>
                                <div class="position67 section02-talk-part">
                                </div>`
    }
    document.getElementById("statusimg").src="/img/v2/talk/AI_talk.gif";
    document.querySelector('#template-section').style.visibility = "hidden"
    document.querySelectorAll(".section02").forEach(element => {
        element.classList.remove("disable");
    });
    document.querySelector(".section02 > .section02-talk-part").classList.remove("disable");
    position.forEach(p=>{
        document.querySelector(p).innerHTML="";
        document.querySelector(p).removeAttribute("data-userid");
        document.querySelector(p).classList.remove("activemessage");
        document.querySelector(p).classList.remove("disable");
    });
    responses = res;
    localStorage.setItem("talkList", JSON.stringify(res));
    document.querySelector('.section03').style.display = 'none';
    // document.querySelector('.section03').classList.add("disable");
    document.querySelector('.section04').classList.add("disable");
    for(var i=0;i< responses.length; i++)
    {
        await render(responses[i]);
        if(i===responses.length - 1)
        {
            await sleep(timeAvatarToballoon);
            document.querySelector('.finish-modal').style.display = 'block';
            document.getElementById("statusimg").src="/img/v2/talk/AI_talk_finished.gif";
        }
    }
}

function playaudio(element){
    console.log(element)
    var snd = new Audio(element.dataset.voiceurl);
    snd.play();
    snd.currentTime=0;
}