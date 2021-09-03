<div class="title_area">
    <div class="modal_movie_header">
        <span class="modal_header_bar"></span>
        <a data-izimodal-close="" href="#">
            <span class="modal_header_link">×</span>
        </a>
        <span class="modal_title_header">タイトル</span>
        <div class="title_selected" id="title_selected">決定</div>
    </div>
    <div class="title_box">
        <input type="text" name="title" maxlength="15" class="title" id="title_input" placeholder="15文字まで入力可">
    </div>
</div>
<script>
    $(function () {
        $("#title_input").keydown(function (event) {
            if (event.key === "Enter") {
                $("#title").val($(".title").val());
                $("#fakeLoader").fakeLoader('start');
                $("#movie_create").submit();
            }
        });
    });
</script>