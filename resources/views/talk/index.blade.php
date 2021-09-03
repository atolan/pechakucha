<!doctype html>
@include('common.head', [
    'title' => 'ペチャクチャ | PeChaKuCha',
    'description' => '',
    'keywords' => '',
    'verification' => '',
    'talkjs' => true,
    'moviejs' => false
    ]
)
<script src="js/noBounce.js" type="text/javascript"></script>
<body>
      
      <div class="container">
        <div class="section01">
            <a id="back_button_header_top"  class="section01-part01"><img src="{{asset('/img/v2/talk/cancel-btn.svg')}}" alt=""></a>
            <img id="statusimg" class="section01-part02" src="{{asset('/img/v2/talk/AI_talk.gif')}}" alt="">
        </div>
        <div class="section02" id="background">
          <div class="section02-talk-part template-section">
            <div style="visibility: hidden" id="template-section" class="section02-part01">
              <div class="section02-part01-up-text">
                トークテーマ
              </div>
              <div id="template_name" class="section02-part01-down-text">
              {{$template->name}}
              </div>
              <a id="newtalk" class="section02-part01-btn">
                <img src="{{asset('/img/v2/talk/button1.png')}}" alt="">
              </a>
            </div>
          </div>
          <div class="seciton02-message-area" id="message-area">
            <div class="position01 section02-talk-part">
            </div>
            <div class="position23 section02-talk-part">
            </div>
            <div class="position45 section02-talk-part">
            </div>
            <div class="position67 section02-talk-part">
            </div>
          </div>

        </div>
        <div class="section03">
          <div class="swiper-pagination"></div>
          <div class="section03-part">
         
          <img src="{{asset('/img/v2/talk/123.png')}}" alt="" class="section03-part-array01 prevemessage">
            <img src="{{asset('/img/v2/talk/124.png')}}" alt="" class="section03-part-array02 nextmessage">
            <a class="section03-part-btn prevemessage btn">
              <img src="{{asset('/img/v2/talk/invalid-name-left.png')}}" alt="">
            </a>
            <a id="changemessage" class="section03-part-dialogue">
              <img src="{{asset('/img/v2/talk/button3.png')}}" alt="">
            </a>
            <a id="changeavatar" class="section03-part-dialogue">
              <img src="{{asset('/img/v2/talk/button2.png')}}" alt="">
            </a>
            <a class="section03-part-btn nextmessage btn">
              <img src="{{asset('/img/v2/talk/invalid-name-right.png')}}" alt="">
            </a>
          </div>
        </div>
        <div class="section04">
          <div class="section04-part">
            <a class="section04-again" id="update">
              <img src="{{asset('/img/v2/talk/random.png')}}" alt="">もう一度再生
            </a>
            <a class="section04-success" id="create_movie">
              <img src="{{asset('/img/v2/talk/movie.svg')}}" alt="">これで完成！
            </a>
          </div>
        </div>
      </div>

      <div class="finish-modal">
      タップ（クリック）すると<br/>
      セリフやキャラを<br/>
      変えられます
      </div>


    @include('common.firebase')
    <div id="message_conf_top" style="display:none;">
        <p>TOP画面に戻ります。<br>よろしいですか？</p>
    </div>
</body>
<div class="speaker_list" id="listener"></div>
<div class="speaker_filter_modal"></div>
<div class="talk_image_list"></div>
<div class="template_list"></div>
<div class="talk_change"></div>
<div class="word_set"></div>
<div class="resize_image">
    <div class="modal_resize_header">
        <a data-izimodal-close="" href="#">
            <span class="modal_header_link">×</span>
        </a>
        <span class="modal_resize_title">画像を編集</span>
        <div class="resize_upload" id="resize_upload">決定</div>
    </div>
    <div id="upload-demo"></div>
</div>
</div>
<div id="fakeLoader"></div>
<div class="create_movie" style="display: none">
    <img id="loading_movie" src="{{asset('/img/v2/talk/loading.gif')}}">
</div>
<input type=hidden id="template_id" value={{$template->template_id}}>
<input type=hidden id="loader_icon_path" value="{{ asset('/img/v2/talk/loading.gif') }}">
<input type=hidden id="push_image" value="">
<input type=hidden id="position" value="">
<input type=hidden id="init_flg" value={{$init_flg}}>
<input type=hidden id="background" value="">
<input type=hidden id="fixed_positions" value="{{implode(',', $fixed_positions)}}">

{{Form::open(['url' => '/movie', 'id' => 'movie_create', 'files' => true])}}
{{Form::close()}}
</html>
<script>
    var talk_list = @json($talk_list); 
    toastr.options = {
        "closeButton": false,
        "positionClass": "toast-center",
        "timeOut": "0",
    }
    // window.onload = function() {
    // // webView.scrollView.bounces = NO;
    //   noBounce.init({
    //     animate: true,
    //     element: document.getElementById("message-area")
    //   }); 
    // }
</script>
