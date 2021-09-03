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
    <div class="usage_area">
        <div class="top_ttl">
            <img alt="" src="{{ asset('/img/v2/top/title@2x.png') }}">
        </div>
        <a id="usage"></a>
        <div class="usage_box">
            <img alt="" class="usage_image" src="{{ asset('/img/v2/usage/p1.png') }}">
            <p class="usage_title1">ＡＩ同士の妄想トークを作ろう</p>
            <p class="usage_content">‟誰か(PeCha)”と‟誰か(KuCha)”のおしゃべりを、ＡＩと一緒に妄想しながら作っちゃおう。<br />
                ペチャとクチャの組み合わせは自由自在。ＡＩ同士のあり得ないトークが誕生する！</p>
        </div>
        <div class="usage_box">
            <img alt="" class="usage_image" src="{{ asset('/img/v2/usage/p2.png') }}">
            <p class="usage_content">好きなPeChaの画像を選択します。各PeChaはオリジナルに学習したＡＩです。</p>
            <img alt="" class="usage_image" src="{{ asset('/img/v2/usage/p3.png') }}">
            <p class="usage_content">吹き出しをタップすると、他の会話候補をＡＩが計算。自分でトークをアレンジできます。</p>
            <img alt="" class="usage_image" src="{{ asset('/img/v2/usage/p4.png') }}">
            <p class="usage_content">完成した妄想トークのスクショ写真や動画をシェアできます。</p>
        </div>
        <div class="usage_box">
            <img alt="" class="usage_image" src="{{ asset('/img/v2/usage/p5.png') }}">
        </div>
        <div class="usage_footer">
            <img alt="" class="top_footer_make" src="{{ asset('/img/v2/usage/p6.png') }}">
            <div class="top_footer_copy"></div>
            <br />
        </div>
    </div>
    <footer>
        <div class="footer_area">
            <div class="footer_left" id="recreate">
                <a href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24">
                        <path fill="#999" d="M4.534 23.715c1.335 0 4.617-1.442 6.55-2.813.188-.14.364-.199.528-.187.14.012.28.012.41.012 7.992 0 13.43-4.489 13.43-10.16 0-5.626-5.625-10.16-12.668-10.16C5.752.406.116 4.94.116 10.566c0 3.562 2.18 6.703 5.753 8.624.188.094.247.258.141.446-.621 1.031-1.723 2.25-2.144 2.812-.457.574-.2 1.266.668 1.266z"/>
                    </svg><br>
                    <font color="#999999">みんなの投稿</font>
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
                <a href="#usage">
                    <div class="footer_right_area">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="20" viewBox="0 0 24 20">
                            <path fill="#21335B" d="M1.912 19.84c.434 0 .715-.152.997-.387.656-.574 2.191-1.289 4.218-1.289 1.828 0 3.059.68 3.61 1.066.199.106.75.434.867.457V2.802C10.877 1.465 8.756.398 6.564.398 3.777.398 1.433 1.992.882 3.105v15.633c0 .785.457 1.102 1.031 1.102zm20.754 0c.575 0 1.032-.317 1.032-1.102V3.105C23.147 1.992 20.815.398 18.026.398c-2.192 0-4.313 1.067-5.04 2.403v16.898c.118-.012.669-.351.88-.469.539-.386 1.77-1.066 3.609-1.066 2.016 0 3.539.715 4.207 1.29.27.222.562.386.984.386z"/>
                        </svg>
                        <br>
                        <font color="#21335B">使い方</font>
                    </div>
                </a>
            </div>
    </footer>
    <input type=hidden id="push_image" value="">
    <div class="speaker_list"></div>
    <div id="fakeLoader"></div>
    @include('common.firebase')
</div>
</body>
<script type="text/javascript">
    $(function () {
        $(document).on('click', '#recalculation', function (e) {
            $("#fakeLoader").fakeLoader('start');
            let talker_list = {};
            talker_list.mode = 'recalculation'
            create_talk(talker_list);
        });
    });

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
    // {{--失敗時--}}
    @if (session('msg_error'))
        $(function () {
            toastr.error('{!! session('msg_error') !!}')
        })
    @endif
</script>
</html>
