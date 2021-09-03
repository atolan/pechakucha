<div class="modal_image_header">
    <span class="modal_header_bar"></span>
    <a data-izimodal-close="" href="#">
        <span class="modal_header_link">×</span>
    </a>
    <span class="modal_header_title">画像を選択</span>
</div>
<div class="image_area">
    <div class="image_box">
        <ul id="talk_images">
            @foreach ($image_list as $key => $image)
                @if(0 == $key)
                    <li>
                        <label style="background-image:url({{ asset('/img/v2/common/camera2x.png') }});width:100%;display: block;height: 100%;background-repeat: no-repeat;background-size: contain; background-position: top center;">
                            <input type="file" name="image_file_photo" style="display:none;" accept=".jpg,.png,.jpeg,.gif">
                        </label>
                    </li>
                @else
                    <li><img src="{{$image->img_url}}" alt="" id="{{$image->img_id}}"></li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
