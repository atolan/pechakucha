@if (!empty($alternative->status->max_num))
    @for ($i = 0; $i < $alternative->status->max_num; $i++)
        @if(0 == $position % 2)
            <li class="change_balloon_l replace"  data-click="{{$alternative->responses[$i]->click}}" data-remark-id="{{$alternative->responses[$i]->remark_id}}">
        @else
            <li class="change_balloon_r replace" data-click="{{$alternative->responses[$i]->click}}" data-remark-id="{{$alternative->responses[$i]->remark_id}}">
        @endif
                <div class="change_faceicon">
                    @if(!empty($alternative->responses[$i]->img))
                        <img src="{{$alternative->responses[$i]->img}}">
                    @else
                        <img src="{{ asset('/img/v2/talk/noarvartar.png') }}" alt="">
                    @endif
                </div>
                @if(!empty($alternative->responses[$i]->content))
                    <p class="change_says">
                        {{$alternative->responses[$i]->content}}
                    </p>
                @else
                    <p class="change_says">
                        考え中...
                    </p>
                @endif
            </li>
    @endfor
@endif
