
    <div class="change_box">
        @if($position % 2 == 0)
            <ul id="images" style="margin-bottom: 0px">
        @else
            <ul id="images" style="margin-bottom: 60px">
        @endif
        @foreach ($list as $key => $speaker)
            @if(0 == $key)
                <li>
                    <label style="background-image:url({{ asset('/img/v2/common/camera2x.png') }});width: 100%;display: block;height: 80%;margin: 18px 12px;background-repeat: no-repeat;background-size: contain;">
                        <input type="file" name="file_photo" style="display:none;" accept=".jpg,.png,.jpeg,.gif">
                    </label>
                </li>
            @else
                <li>
                    @if($img_id == $speaker->ai_id)
                        <img class="selected" src="{{$speaker->img_url}}" alt="" id="{{$speaker->ai_id}}">
                            <div class="check"><img src="/img/v2/movie/check_white.png" alt=""></div>
                    @else
                        <img src="{{$speaker->img_url}}" alt="" id="{{$speaker->ai_id}}">
                    @endif
                </li>
            @endif
        @endforeach
            </ul>
    </div>
