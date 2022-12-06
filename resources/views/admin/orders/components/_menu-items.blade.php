@isset($item)
    <div class="col-md-3 menu-items">
        <div class="food-item-card text-center">
            {{-- <div class="food-item-image">
                <img src="{{ $item->image() }}" alt="food-item"></a>
            </div> --}}
            <div class="food-item-content">
                <b>
                    <h5 class="food-item-name">{{ $item->name }}</h5>
                </b>
                <h5 class="food-item-price"><span>Rs. {{ $item->price }}</h5><button data-id="{{ $item->id }}"
                    data-price="{{ $item->price }}" data-name="{{ $item->name }}"
                    class="food-item-add  btn-success add-item" title="Add to Cart"><i class="fas fa-cart-plus"></i><span>
                        Add</span></button>
            </div>
        </div>
    </div>
@endisset
