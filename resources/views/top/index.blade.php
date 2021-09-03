<!doctype html>
<html>
@include('common.head', [
    'title' => 'ペチャクチャ | PeChaKuCha',
    'description' => '誰か（ペチャ）と誰か（クチャ）のおしゃべりを、ＡＩと一緒に妄想しながら作っちゃおう。ペチャとクチャの組み合わせは自由自在。おもしろトーク作成サイト。',
    'keywords' => 'ペチャクチャ,pechakucha,大喜利AI,大喜利人工知能',
    'verification' => '_3DfQuUF_ZLmhVHxWLeE3U-FNICuaUbBXTMLgFHwLf8',
    'talkjs' => false,
    'moviejs' => false
    ]
)
<body>
<div class="contents_area">
<div class="top_area">
    <div class="top_ttl">
        <img alt="" src="{{ asset('/img/v2/top/title@2x.png') }}">
    </div>
    @if(!empty($popular_images))
    <div class="popular_header">
        <a id="popular">🏆いま人気</a>
    </div>
        @foreach($popular_images as $ipath)
            <div class="popular_box">
                <a href="{{$ipath->img_url}}" class="popular_modal">
                    <img
                            @if($ipath->screen_shot_height > 663)
                            class="popular_image popular_image_fix"
                            @else
                            class="popular_image"
                            @endif
                            alt="" src="{{$ipath->img_url}}"
                    >
                </a>
            </div>
        @endforeach
    @endif
    <div class="top_footer">
        <img alt="" class="top_footer_logo" src="{{ asset('/img/v2/top/logo-powerdby@2x.png') }}">
        <div class="top_twitter_area">
            <a href="https://twitter.com/intent/follow?screen_name=PeChaKuCha_AI" target="_blank" onclick="window.open(this.href, 'window', 'width=600, height=400, menubar=no, toolbar=no, scrollbars=yes'); return false;" target="_blank">
                <img alt="" src="{{ asset('/img/v2/top/twitter.png') }}"> @PeChaKuCha_AIをフォロー
            </a>
        </div>
        <div class="top_footer_copy">
            ©PeCha-KuCha
        </div>
        <br />
    </div>
</div>
<footer>
    <div class="footer_area">
        <div class="footer_left" id="recreate">
            <a href="#popular">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24">
                    <path fill="#21335B" d="M4.534 23.715c1.335 0 4.617-1.442 6.55-2.813.188-.14.364-.199.528-.187.14.012.28.012.41.012 7.992 0 13.43-4.489 13.43-10.16 0-5.626-5.625-10.16-12.668-10.16C5.752.406.116 4.94.116 10.566c0 3.562 2.18 6.703 5.753 8.624.188.094.247.258.141.446-.621 1.031-1.723 2.25-2.144 2.812-.457.574-.2 1.266.668 1.266z"/>
                </svg><br>
                <font color="#21335B">みんなの投稿</font>
            </a>
        </div>
        <div class="footer_center">
            <a href="/talk">
                <div class="footer_center_area">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                        <path fill="#FFF" fill-rule="evenodd" d="M14.333 0c1.105 0 2 .895 2 2v9.666H26c1.105 0 2 .896 2 2v.667c0 1.105-.895 2-2 2h-9.667V26c0 1.105-.895 2-2 2h-.666c-1.105 0-2-.895-2-2l-.001-9.667H2c-1.105 0-2-.895-2-2v-.666c0-1.105.895-2 2-2l9.666-.001V2c0-1.105.896-2 2-2h.667z"/>
                    </svg>
                </div>
                新しく作る
            </a>
        </div>
        <div class="footer_right">
            <a href="/usage">
                <div class="footer_right_area">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="20" viewBox="0 0 24 20">
                        <path fill="#999" d="M1.912 19.84c.434 0 .715-.152.997-.387.656-.574 2.191-1.289 4.218-1.289 1.828 0 3.059.68 3.61 1.066.199.106.75.434.867.457V2.802C10.877 1.465 8.756.398 6.564.398 3.777.398 1.433 1.992.882 3.105v15.633c0 .785.457 1.102 1.031 1.102zm20.754 0c.575 0 1.032-.317 1.032-1.102V3.105C23.147 1.992 20.815.398 18.026.398c-2.192 0-4.313 1.067-5.04 2.403v16.898c.118-.012.669-.351.88-.469.539-.386 1.77-1.066 3.609-1.066 2.016 0 3.539.715 4.207 1.29.27.222.562.386.984.386z"/>
                    </svg>
                    <br>
                    <font color="#999999">使い方</font>
                </div>
            </a>
    </div>
</footer>
<input type=hidden id="push_image" value="">
<div class="popular_modal"></div>
<div id="fakeLoader"></div>
@include('common.firebase')
</div>
</body>
<script type="text/javascript">
    toastr.options = {
        "closeButton": false,
        "positionClass": "toast-center",
        "timeOut": "0",
    }

    // {{--成功時--}}
    @if (session('msg_success'))
        $(function () {
            toastr.success('{!! session('msg_success') !!}');
        })
    @endif
    // {{--失敗時--}}
    @if (session('msg_error'))
        $(function () {
            toastr.error('{!! session('msg_error') !!}')
        })
    @endif
</script>
</html>
