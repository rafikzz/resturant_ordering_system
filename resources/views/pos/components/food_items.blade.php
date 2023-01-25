@foreach ($categories as $key => $category)
    <div class="tab_content {{ $loop->first ? 'active' : '' }}" data-tab="{{ $category->title }}">
        <div class="row ">
            @foreach ($category->active_items as $item)
                <div class="col-lg-3 col-sm-6 d-flex align-items-stretch menu-items">
                    <div class="productset d-flex flex-column ">
                        <div class="productsetimg">
                            <img src="{{ $item->image() }}" alt="img">
                            <h6>Qty: 1</h6>
                            {{-- <div class="check-product">
                                <i class="fa fa-check"></i>
                            </div> --}}
                        </div>
                        <div class="productsetcontent">
                            <h5>{{ $category->title }}</h5>
                            <h4>{{ $item->name }}</h4>
                            <h6 class="staff-price" style="display:{{ $guest_menu ? 'none' : 'block' }};">{{ $item->price }}
                            </h6>
                            <h6 class="guest-price" style="display:{{ $guest_menu ? 'block' : 'none' }};">
                                {{ $item->guest_price }}</h6>

                        </div>
                        <button data-id="{{ $item->id }}" data-price="{{ $item->price }}"
                            data-name="{{ $item->name }}" class="food-item-add  btn-secondary add-item mt-auto"
                            title="Add to Cart"><i class="fas fa-cart-plus"></i><span>
                                Add</span></button>
                    </div>

                </div>

            @endforeach
        </div>
    </div>
@endforeach
