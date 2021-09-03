
    <div class="modal_comment_area" id="comment_change">
    </div>
    <div class="modal_change_header">
        <span class="modal_header_bar"></span>
        <a data-izimodal-close="" href="#">
            <span class="modal_header_link">×</span>
        </a>
        <span class="modal_header_title">トークを編集</span>
        <div class="candidate candidate_ng" id="candidate">他の候補</div>
        <ul>
            <li class="change_talk_box_li" data-click=2 id="word_set">
                    <div class="talk_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="29" height="19" viewBox="0 0 29 19">
                            <path fill="#666" d="M27.557 3.22l.703-.722c.342-.352.351-.82.01-1.162l-.225-.225c-.303-.302-.791-.263-1.113.059l-.713.693 1.338 1.358zM17 12.704l1.904-.84 7.969-7.968-1.348-1.329-7.959 7.97-.888 1.845c-.078.185.127.41.322.322zm4.97 6.162c1.739 0 2.706-.927 2.706-2.666V8.426l-1.612 1.611v6.152c0 .752-.43 1.182-1.181 1.182H3.503c-.751 0-1.171-.43-1.171-1.182V8.445c0-.752.42-1.172 1.172-1.172h14.99l1.504-1.503H3.426C1.678 5.77.71 6.697.71 8.445V16.2c0 1.738.967 2.666 2.715 2.666H21.97zM5.888 13.543c.674 0 1.23-.557 1.23-1.22 0-.675-.556-1.221-1.23-1.221-.674 0-1.221.546-1.221 1.22 0 .664.547 1.221 1.22 1.221zm3.75 0c.683 0 1.23-.557 1.23-1.22 0-.675-.547-1.221-1.23-1.221-.684 0-1.221.546-1.221 1.22 0 .664.547 1.221 1.22 1.221zm3.75 0c.674 0 1.22-.557 1.22-1.22 0-.675-.546-1.221-1.22-1.221-.674 0-1.221.546-1.221 1.22 0 .664.557 1.221 1.22 1.221z"/>
                        </svg>
                    </div>
                    <p class="talk_icon_sentence">
                        自分で入力する
                    </p>
                </li>
                <li class="change_talk_box_li" data-click=2 id="talk_image_list">
                    <div class="talk_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="18" viewBox="0 0 23 18">
                            <path fill="#666" d="M19.23 17.895c1.954 0 2.95-.987 2.95-2.92V3.705c0-1.924-.996-2.91-2.95-2.91H3.303c-1.953 0-2.95.976-2.95 2.91v11.27c0 1.933.997 2.92 2.95 2.92H19.23zM1.965 13.89V3.812c0-.937.498-1.406 1.396-1.406h15.81c.89 0 1.397.469 1.397 1.406v10.04L15.734 9.31c-.41-.362-.888-.557-1.386-.557-.508 0-.967.166-1.387.547l-4.17 3.73-1.709-1.543c-.39-.351-.82-.527-1.25-.527-.42 0-.81.166-1.191.518L1.965 13.89zm5.37-4.444c1.202 0 2.188-.976 2.188-2.187 0-1.201-.986-2.197-2.187-2.197-1.211 0-2.188.996-2.188 2.197 0 1.21.977 2.187 2.188 2.187z"/>
                        </svg>
                    </div>
                    <p class="talk_icon_sentence">
                        写真を投稿
                    </p>
                </li>    
        </ul>
    </div>
    <div class="change_talk_box">
       <ul>
            <div class="change_list">
                @include('talk.change_list', ['alternative' => $alternative, 'position' => $position])
            </div>
        </ul>
    </div>
    <div class="footer_change_talk">
        <div class="static_talk_box">
            <div class="talk_icon_lock">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19">
                    <path fill="#666" d="M10.92 18.129c1.212 0 1.798-.596 1.798-1.914V9.389c0-1.143-.46-1.748-1.397-1.875v-2.51c0-2.266 1.446-3.477 3.155-3.477 1.699 0 3.144 1.211 3.144 3.477v2.021c0 .586.352.89.8.89.43 0 .782-.274.782-.89V5.19c0-3.486-2.305-5.175-4.726-5.175-2.432 0-4.737 1.69-4.737 5.175v2.295l-7.49.02c-1.21 0-1.982.596-1.982 1.885v6.826c0 1.318.595 1.914 1.806 1.914h8.848z"/>
                </svg>
            </div>
            <span style="width:65%">
                <p>トークを固定</p>
            </span>
            <span style="width:20%">
                <div class="switchArea">
                    <input type="checkbox" id="switch1">
                    <label for="switch1" id="switchLabel"><span></span></label>
                    <div id="swImg"></div>
                </div>
            </span>
        </div>
        <span class="footer_comment">
            トークを変更した時、固定されたトークは再計算しないようにできます。
        </span>
        <input type="hidden" id="is_reload" value={{(int)$is_reload}}>

    </div>
<script>
$(function () {
    var fixed  = $("#fixed_positions").val();
    var index = fixed.indexOf("{{$position}}");
    if (index !== -1) {
        $('#switch1').prop("checked",true);
        $(".talk_box").prop('disabled', true);
        $(".change_talk_box ul").addClass('fixed');
    }
});
</script>
