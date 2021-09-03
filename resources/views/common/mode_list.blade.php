<div class="mode_area">
    <div class="modal_movie_header">
        <span class="modal_header_bar"></span>
        <a data-izimodal-close="" href="#">
            <span class="modal_header_link">×</span>
        </a>
        <span class="modal_header_title">画面モードを選択</span>
        <div class="mode_selected" id="mode_selected" style="display: none">決定</div>
    </div>
    <div class="mode_area">
        <div class="mode_box">
            @if(1 < count($mode_list))
                <p class="mode_title">画面モード</p>
                <ul id="mode_box">
                    @foreach ($mode_list as $mk => $mv)
                        <li data-mode-id={{$mv->mode_id}}>
                            @if($mv->mode_id == $select_mode_id)
                                <img class="mode_img selected" alt="" src="{{$mv->img_url}}">
                                <div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>
                            @else
                                <img class="mode_img" alt="" src="{{$mv->img_url}}">
                            @endif
                            <span>{{$mv->name}}</span>
                        </li>
                    @endforeach
                </ul>
                <div style="border-bottom: 1px solid #b3b3b3;"></div>
            @endif
            <p class="mode_title">背景</p>
            <ul id="color_box">
                @foreach ($color_list as $ck => $cv)
                    @if(99 == $cv->color_id)
                        <li id="input_photo" data-color-id={{$cv->color_id}}>
                            @if($cv->color_id == $select_color_id)
                                <div class="color_img selected mode_box_image" style="background-color: {{$cv->color_code}}"></div>
                                <div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>
                                <div class="photo"><img src="/img/v2/movie/photo.png" alt=""></div>
                            @else
                                <div class="color_img mode_box_image" style="background-color: {{$cv->color_code}}"></div>
                                <div class="photo"><img src="/img/v2/movie/photo.png" alt=""></div>
                            @endif
                            <span>{{$cv->name}}</span>
                        </li>
                        @continue
                    @endif
                    <li data-color-id={{$cv->color_id}}>
                        @if($cv->color_id == $select_color_id)
                            <div class="color_img selected mode_box_image" style="background-color: {{$cv->color_code}}"></div>
                            <div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>
                        @else
                            <div class="color_img mode_box_image" style="background-color: {{$cv->color_code}}"></div>
                        @endif
                        <span>{{$cv->name}}</span>
                    </li>
                @endforeach
            </ul>
                <div style="border-bottom: 1px solid #b3b3b3;"></div>
        </div>
    </div>
</div>