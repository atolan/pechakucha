
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="google-site-verification" content="{{$verification}}">
    <meta name="description" content="{{$description}}">
    <meta name="keywords" content="{{$keywords}}">
    <script
        src="//code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/comment.css?d=202108292317')}}">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/iziModal.css?d=202108292317')}}" media="screen">
    <link rel="stylesheet" href="{{ asset('css/fakeLoader.css')}}">
    <link rel="stylesheet" href="{{ asset('css/select-css.css?d=202108292317')}}">
    <link rel="stylesheet" href="{{ asset('css/colorbox.css?d=202108292317') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('/js/toastr.min.js')}}"></script>
    <script src="{{ asset('/js/fakeLoader.js?d=202108292317')}}"></script>
    <script src="{{ asset('/js/iziModal.js') }}"></script>
    <script src="{{ asset('/js/jquery.colorbox-min.js') }}"></script>
    <link rel="apple-touch-icon" href="{{asset('/img/v2/top/icon180.png')}}" />
    <link rel="icon" type="image/png" href="{{asset('/img/v2/top/icon192.png')}}">
    <meta property="og:title" content="妄想AIトーク PeChaKuCha" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://pechakucha.ai" />
    <meta property="og:image" content="{{asset('/img/v2/top/ogp.png')}}" />
    <meta property="og:site_name" content="妄想AIトーク PeChaKuCha" />
    <meta property="og:description" content='‟誰か(PeCha)”と‟誰か(KuCha)”のおしゃべりを、ＡＩと一緒に妄想しながら作っちゃおう。ペチャとクチャの組み合わせは自由自在。ＡＩ同士のあり得ないトークが誕生する！' />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@PeChaKuCha_AI" />
    <meta property="fb:app_id" content="297196871512981" />
    <script src="{{ asset('/js/top.js?d=202108292317') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/croppie.css') }}">
    <script src="{{ asset('/js/croppie.js') }}"></script>
    @if($talkjs)
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="{{ asset('/js/talk.js?d=202108292317') }}"></script>
        <script src="{{ asset('/js/genre.js?d=202108292317') }}"></script>
        <script src="{{ asset('/js/swiper-bundle.min.js?d=202108292317') }}"></script>
        
    @endif
    @if($moviejs)
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="{{ asset('css/movie.css?d=202108292317') }}">
        <script src="{{ asset('/js/movie.js?d=202108292317') }}"></script>
        <script>
            window.twttr = (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                    t = window.twttr || {};
                if (d.getElementById(id)) return t;
                js = d.createElement(s);
                js.id = id;
                js.src = "//platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);

                t._e = [];
                t.ready = function(f) {
                    t._e.push(f);
                };
                return t;
            }(document, "script", "twitter-wjs"));
        </script>
    @endif
    <title>{{$title}}</title>
</head>

