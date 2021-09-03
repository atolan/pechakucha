@if(!empty($talk_list))
@foreach($talk_list->responses as $key => $talker)
    <div class="@if(0 == $talker->position % 2) balloon_l  @else balloon_r @endif comment" data-position="{{$talker->position}}">
        <div class="faceicon image_list" data-img-id="{{$talker->ai_id}}">
            @if(!empty($talker->ai_img_url))
                <img src="{{$talker->ai_img_url}}" alt="">
            @else
                <img src="{{ asset('/img/v2/talk/white.png') }}" alt="">
            @endif
        </div>
        <div class="talk_contents_area tap" data-position="{{$talker->position}}">
            @if($talker->content_type == 'text')
                @if(!empty($fixed_positions) && in_array($talker->position, $fixed_positions))
                    <p class="locked">{{$talker->content}}</p>
                @else
                    <p class="says">{{$talker->content}}</p>
                @endif
            @else
                @if(!empty($fixed_positions) && in_array($talker->position, $fixed_positions))
                    <div class="file_img_locked tap">
                        <img src="{{$talker->content}}" alt="{{$talker->img_text}}">
                @else
                    <div class="file_img tap">
                        <img src="{{$talker->content}}" alt="{{$talker->img_text}}">
                @endif
                    </div>
            @endif
        </div>
	</div>
@endforeach
@else
    <div class="balloon_l">
        <div class="faceicon" data-push="talker">
            <img src="{{ asset('/img/v2/talk/white.png') }}" alt="">
        </div>
        <p class="think tap">AIが思考中<img src="/img/v2/talk/dot_think_l@3x.png"></p>
    </div>
@endif
