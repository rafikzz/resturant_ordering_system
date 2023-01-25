<li class="" id="all-items">
    <div class="product-details ">
        <h6>All</h6>
    </div>
</li>
@foreach ($categories as $category)
    <li class="{{ $loop->first?'active':'' }}" id="{{ $category->title }}">
        <div class="product-details ">
            <h6>{{ $category->title }}</h6>
        </div>
    </li>
@endforeach
