@if($init_flg)
<div class="modal_speaker_area-blue">
@else
<div class="modal_speaker_area" id="speaker-change">
@endif
    <div class="section02-talk-part">
        @if($init_flg)
            <div class="section02-part02 mb-25">
                <div class="section02-part02-img">
                    <img src="{{ asset('/img/v2/talk/noarvartar.png') }}" alt="">
                </div>
                <div class="section02-part02-text b-15">
                    AIが思考中<img src="/img/v2/talk/dot_think_l@3x.png">
                </div>
            </div>
        @else
            @if(0 == $position % 2)
                <div class="section02-part02 mb-25">
                    <div class="section02-part02-img">
                        <img src="{{$talk_list->responses[$position]->ai_img_url}}" alt="">
                    </div>
                    <div class="section02-part02-text b-15">
                        PeChaを変更中<img src="/img/v2/talk/dot_think_l@3x.png">
                    </div>
                </div>
            @else
                <div class="section02-part02  mb-25">
                    <div></div>                        
                    <div style="order: 1" class="section02-part02-img">
                        <img src="{{$talk_list->responses[$position]->ai_img_url}}" alt="">
                    </div>
                    <div class="section02-part02-text01 b-15">
                     KuChaを変更中
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
<div class="speaker_area">
    <div class="modal_speaker_header">
        <span class="modal_header_bar"></span> 
        <a href="#">
            <span id="backtop" class="modal_header_link">×</span>
        </a>
        @if(0 == $position % 2)
            <span class="modal_header_title">PeChaを選択</span>
        @else
            <span class="modal_header_title">KuChaを選択</span>
        @endif
        <div class="speaker_filter" id="speaker_filter">絞り込み</div>
        @if($init_flg)
        <div class="genre_main">
            <div class="genre_area">
                <span class="theme">トークテーマ</span>
                <span class="genre_name">{{$template? $template->name : ""}}</span>
                <span class="genre_change"><a href="#" id="template_change">変更</a></span>
            </div>
        </div>
        @endif
    </div>
    @if($init_flg)
    <div class="change_area withgenre_main">
            @include('common.speakers', ['position' => $position, 'img_id' => $img_id, 'position' => $position])
    </div>
    @else
    <div class="change_area">
        @include('common.speakers', ['position' => $position, 'img_id' => $img_id, 'position' => $position])
    </div>
    @endif

</div>
<input type=hidden id="category_id" value="0">
