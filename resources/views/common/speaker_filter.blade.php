<div class="category_header">
    <span class="filter_cancel">絞り込み解除</span>
</div>
<div class="category_area">
    <ul id="category_list">
        @foreach ($categories as $key => $category)
            <li class="{{$category->category_id % 2 ? 'category_left' : 'category_right'}} {{$category->category_id == $category_id ? 'category_select' : ''}}" data-category-id={{$category->category_id}}>
                <span class="category_box">
                    <p>{{$category->name}}</p>
                </span>
            </li>
        @endforeach
    </ul>
</div>
