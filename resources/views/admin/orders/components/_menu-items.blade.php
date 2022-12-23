@isset($item)
    <div class="col-md-3 menu-items  mb-3 d-flex align-items-stretch">
            <div class="food-item-card text-center card card-body d-flex flex-column ">
                {{-- <div class="food-item-image">
                    <img src="{{ $item->image() }}" alt="food-item"></a>
                </div> --}}
                <div class="food-item-content">
                    <b>
                        <h5 class="food-item-name">{{ $item->name }}</h5>
                    </b>
                    <h5 class="food-item-price guest-price"
                        style="display: {{ old('guest_price', $guest_menu) ? 'block' : 'none' }}">
                        <span>Rs. {{ $item->guest_price }}
                    </h5>
                    <h5 class="food-item-price staff-price mt-auto"
                        style="display: {{ old('guest_price', $guest_menu) ? 'none' : 'block' }}">
                        <span>Rs. {{ $item->price }}
                    </h5>
                </div>

                <button data-id="{{ $item->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}"
                    class="food-item-add  btn-success add-item  mt-auto" title="Add to Cart"><i
                        class="fas fa-cart-plus"></i><span>
                        Add</span></button>
            </div>
    </div>
@endisset
