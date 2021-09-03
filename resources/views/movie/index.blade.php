<!doctype html>
<html>
@include('common.head', [
    'title' => 'ペチャクチャ | PeChaKuCha',
    'description' => '',
    'keywords' => '',
    'verification' => '',
    'talkjs' => false,
    'moviejs' => true
    ]
)
<div>
<div class="contents_area">
<div class="movie">
    <header id="header">
        <a href="#" onclick="return false;">
            <img id="back_button_header_top" alt="" src="{{ asset('/img/v2/talk/close.png') }}">
        </a>
        <span>トーク完成！</span>
    </header>
    <div class="header_modal"></div>

    <div class="movie_area">        
        <div class="movie_box">
            <span>動画</span>
            <div class="movie_box_video">
                <video id="talk_movie" src="{{$movie_info->movie_url}}" autoplay muted controls></video>
            </div>
            <div class="flex_box">
                <div class="share_box">
                    <a id="twitter_message_movie" href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16">
                            <path fill="#FFF" d="M20 1.896c-.737.32-1.526.537-2.356.637.847-.5 1.5-1.291 1.805-2.233-.793.462-1.67.8-2.606.98C16.093.491 15.024 0 13.846 0 11.58 0 9.744 1.808 9.744 4.037c0 .317.033.625.106.921-3.412-.166-6.438-1.775-8.46-4.22-.352.595-.555 1.291-.555 2.029 0 1.4.729 2.637 1.83 3.362-.677-.016-1.313-.2-1.864-.504v.05c0 1.958 1.416 3.587 3.293 3.958-.343.092-.708.142-1.08.142-.263 0-.522-.025-.772-.075.521 1.604 2.039 2.77 3.836 2.804-1.403 1.084-3.175 1.73-5.099 1.73-.33 0-.657-.021-.979-.059C1.81 15.333 3.967 16 6.281 16c7.557 0 11.685-6.154 11.685-11.492 0-.175-.005-.35-.013-.52.801-.571 1.496-1.28 2.047-2.092z"/>
                        </svg>
                        <span>ツイート</span>
                    </a>
                </div>
                <div class="download_box">
                    <a href="/movie/download">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="22" viewBox="0 0 19 22">
                            <path fill="#FFF" d="M9.484 15.139c.264 0 .528-.098.791-.362l3.262-3.134c.176-.176.283-.372.283-.635 0-.508-.42-.86-.898-.86-.254 0-.498.098-.674.293l-1.27 1.348-.605.752.127-1.445v-9.98c0-.538-.45-1.007-1.016-1.007-.566 0-1.025.47-1.025 1.006v9.98l.127 1.446-.606-.752-1.27-1.348c-.175-.195-.419-.293-.673-.293-.488 0-.898.352-.898.86 0 .263.107.459.283.635l3.262 3.134c.263.264.527.362.8.362zm5.616 6.23c2.177 0 3.33-1.152 3.33-3.31V8.576c0-2.158-1.153-3.31-3.33-3.31H12.53v2.236h2.364c.84 0 1.308.43 1.308 1.318v9.004c0 .88-.469 1.309-1.308 1.309H4.025c-.85 0-1.298-.43-1.298-1.309V8.82c0-.888.449-1.318 1.298-1.318h2.403V5.266H3.82c-2.168 0-3.33 1.152-3.33 3.31v9.483c0 2.158 1.162 3.31 3.33 3.31H15.1z"/>
                        </svg>
                        <span>保存</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer_box">
            <div class="recreate_box">
                <div id="back_button_footer_talk">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <path fill="#999" fill-rule="evenodd" d="M10 0c.828 0 1.5.672 1.5 1.5v7h7c.828 0 1.5.672 1.5 1.5s-.672 1.5-1.5 1.5h-7.001l.001 7c0 .828-.672 1.5-1.5 1.5s-1.5-.672-1.5-1.5l-.001-7H1.5C.672 11.5 0 10.828 0 10s.672-1.5 1.5-1.5h7v-7C8.5.672 9.172 0 10 0z"/>
                    </svg>
                    <span>もう一度作る</span>
                </div>
            </div>
            <div class="post_box">
                <div id="back_button_footer_top">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24">
                        <path fill="#999999" d="M4.534 23.715c1.335 0 4.617-1.442 6.55-2.813.188-.14.364-.199.528-.187.14.012.28.012.41.012 7.992 0 13.43-4.489 13.43-10.16 0-5.626-5.625-10.16-12.668-10.16C5.752.406.116 4.94.116 10.566c0 3.562 2.18 6.703 5.753 8.624.188.094.247.258.141.446-.621 1.031-1.723 2.25-2.144 2.812-.457.574-.2 1.266.668 1.266z"/>
                    </svg>
                    <span>みんなの投稿を見る</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="title_modal"></div>
</div>
<div class="footer_modal"></div>
<div id="fakeLoader"></div>

{{Form::open(['url' => '/movie/send_twitter', 'id' => 'twitter_send'])}}
{{Form::hidden('content', "", ['id' => 'twitter_content'])}}
{{Form::hidden('send_type', "", ['id' => 'send_type'])}}
{{Form::close()}}

{{Form::open(['url' => '/movie', 'id' => 'movie_create', 'files' => true])}}
{{Form::file('upload_photo', ['id' => 'upload_photo', 'style' => "display:none;", 'accept' => ".jpg,.png,.jpeg,.gif"])}}
{{Form::hidden('mode_id', $mode->mode_id, ['id' => 'mode_id'])}}
{{Form::hidden('color_id', $color->color_id, ['id' => 'color_id'])}}
{{Form::hidden('img_path', $img_path, ['id' => 'img_path'])}}
{{Form::hidden('title', $title, ['id' => 'title'])}}
{{Form::close()}}

{{Form::hidden('default_content_movie', $movie_info->message, ['id' => 'default_content_movie'])}}

{{Form::hidden('bf_mode', $mode->mode_id, ['id' => 'bf_mode'])}}
{{Form::hidden('bf_color', $color->color_id, ['id' => 'bf_color'])}}
<div class="twitter_message"></div>
<div class="mode_list"></div>
@include('common.firebase')
</body>
</html>
<script>

    const appHeight = () => {
        const doc = document.documentElement
        doc.style.setProperty('--app-height', `${window.innerHeight}px`)
    };
    window.addEventListener('resize', appHeight)
    appHeight()

    toastr.options = {
        "closeButton": false,
        "positionClass": "toast-center",
        "timeOut": "0",
    }

    // {{--成功時--}}
    @if (session('msg_success'))
    $(function () {
    toastr.success('{!! session('msg_success') !!}')
    })
    @endif
    // {{--通常時--}}
    @if (session('msg_info'))
    $(function () {
    toastr.info('{!! session('msg_info') !!}')
    })
    @endif
    // {{--失敗時--}}
    @if (session('msg_error'))
    $(function () {
    toastr.error('{!! session('msg_error') !!}')
    })
    @endif
</script>

