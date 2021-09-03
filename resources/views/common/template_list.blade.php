<div class="modal_speaker_area" id="speaker-change">
    <div class="section02-talk-part">
            <div class="section02-part02 mb-25">
                <div class="section02-part02-img">
                    <img src="{{ asset('/img/v2/talk/noarvartar.png') }}" alt="">
                </div>
                <div class="section02-part02-text b-15">
                    AIが思考中<img src="/img/v2/talk/dot_think_l@3x.png">
                </div>
            </div>
    </div>
</div>
<div class="template_area" >
    <div class="modal_header">
        <span class="modal_header_bar"></span>
        <a data-izimodal-close="" href="#">
            <span class="modal_header_link">×</span>
        </a>
        <span class="modal_header_title">トークテーマを変更</span>
    </div>
    <div class="template_box">
        <ul>
            @foreach ($template_list as $v)
                <li class="template_list" data-template-id={{$v->template_id}}>
                    @if($template_id == $v->template_id)
                        <img alt="" src="{{ asset('/img/v2/talk/check.png') }}">
                        <div class="template_checked">
                            <span>{{$v->name}}</span>
                        </div>
                    @else
                        <div class="template_no_checked">
                            <span>{{$v->name}}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>